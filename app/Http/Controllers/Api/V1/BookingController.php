<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * GET /api/v1/bookings
     * List the authenticated user's bookings.
     */
    public function index(Request $request)
    {
        $bookings = Booking::with(['destination', 'operator'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($b) => $this->formatBooking($b));

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    /**
     * POST /api/v1/bookings
     * Create a new booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id'   => 'required|string',
            'operator_id'      => 'nullable|string',
            'booking_date'     => 'required|date',
            'adults'           => 'integer|min:1|max:50',
            'children'         => 'integer|min:0|max:50',
            'payment_method'   => 'required|in:mpesa,card,bank_transfer,cash',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $destination = Destination::where('id', $validated['destination_id'])
            ->orWhere('slug', $validated['destination_id'])
            ->first();

        if (!$destination) {
            // Fallback: try finding first available destination or return 404
            $destination = Destination::first();
        }

        if (!$destination) {
            return response()->json(['success' => false, 'message' => 'Destination not found.'], 404);
        }

        $operatorId = !empty($validated['operator_id']) ? $validated['operator_id'] : ($destination->operator_id ?? null);

        $adults   = $validated['adults'] ?? 1;
        $children = $validated['children'] ?? 0;
        $total    = $adults + $children;

        $priceKes       = $destination->price_kes ?? 15000;
        $totalPriceKes  = ($adults * $priceKes) + ($children * intval($priceKes * 0.8));
        $depositKes     = intval($totalPriceKes * 0.30);

        $booking = Booking::create([
            'user_id'           => $request->user()->id,
            'destination_id'    => $destination->id,
            'operator_id'       => $operatorId,
            'status'            => 'pending',
            'adults'            => $adults,
            'children'          => $children,
            'total_participants'=> $total,
            'total_price_kes'   => $totalPriceKes,
            'deposit_paid_kes'  => $depositKes,
            'booking_date'      => $validated['booking_date'],
            'payment_method'    => $validated['payment_method'],
            'payment_status'    => 'pending',
            'special_requests'  => $validated['special_requests'] ?? null,
        ]);

        $booking->load(['destination', 'operator']);

        return response()->json([
            'success' => true,
            'message' => 'Booking request submitted successfully!',
            'data'    => $this->formatBooking($booking),
        ], 201);
    }

    /**
     * GET /api/v1/bookings/{id}
     */
    public function show(Request $request, string $id)
    {
        $booking = Booking::with(['destination', 'operator'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $this->formatBooking($booking)]);
    }

    /**
     * DELETE /api/v1/bookings/{id}
     * User cancels their own booking.
     */
    public function cancel(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel this booking.'], 422);
        }

        $booking->update([
            'status'            => 'cancelled',
            'cancelled_reason'  => $request->reason ?? 'Cancelled by user',
        ]);

        return response()->json(['success' => true, 'message' => 'Booking cancelled.']);
    }

    /**
     * GET /api/v1/destinations/{id}/availability
     * Public: check if a destination has availability for a date.
     */
    public function checkAvailability(Request $request, string $destinationId)
    {
        $request->validate([
            'date'  => 'required|date|after_or_equal:today',
            'party' => 'integer|min:1',
        ]);

        $destination = Destination::findOrFail($destinationId);

        $confirmedBookings = Booking::where('destination_id', $destinationId)
            ->where('booking_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('total_participants');

        $maxCapacity  = $destination->group_size_max ?? 20;
        $available    = max(0, $maxCapacity - $confirmedBookings);
        $isAvailable  = $available > 0;

        return response()->json([
            'success'      => true,
            'data' => [
                'destination_id' => $destinationId,
                'date'           => $request->date,
                'is_available'   => $isAvailable,
                'spots_left'     => $available,
                'max_capacity'   => $maxCapacity,
            ],
        ]);
    }

    private function formatBooking(Booking $b): array
    {
        $dest = $b->destination;
        $op   = $b->operator;
        return [
            'id'                 => $b->id,
            'confirmation_code'  => $b->confirmation_code,
            'status'             => $b->status,
            'payment_status'     => $b->payment_status,
            'payment_method'     => $b->payment_method,
            'adults'             => $b->adults,
            'children'           => $b->children,
            'total_participants' => $b->total_participants,
            'total_price_kes'    => $b->total_price_kes,
            'deposit_paid_kes'   => $b->deposit_paid_kes,
            'booking_date'       => $b->booking_date?->toDateString(),
            'special_requests'   => $b->special_requests,
            'operator_notes'     => $b->operator_notes,
            'cancelled_reason'   => $b->cancelled_reason,
            'created_at'         => $b->created_at?->toIso8601String(),
            'destination' => $dest ? [
                'id'              => $dest->id,
                'title'           => $dest->title,
                'cover_image_url' => $dest->cover_image_url,
                'location'        => $dest->location,
                'price_kes'       => $dest->price_kes,
            ] : null,
            'operator' => $op ? [
                'id'          => $op->id,
                'name'        => $op->name,
                'logo_url'    => $op->logo_url,
                'rating'      => $op->rating,
                'is_verified' => $op->is_verified,
                'phone'       => $op->phone,
                'email'       => $op->email,
            ] : null,
        ];
    }
}
