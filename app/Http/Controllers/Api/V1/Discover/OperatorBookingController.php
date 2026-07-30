<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use Illuminate\Http\Request;

class OperatorBookingController extends Controller
{
    /**
     * Resolve the operator for the authenticated user.
     * For now, uses operator_id query param or user's first linked operator.
     */
    private function getOperator(Request $request): ?Operator
    {
        // If the user passes operator_id explicitly
        if ($request->has('operator_id')) {
            return Operator::find($request->operator_id);
        }
        // Otherwise try to find by user_id field on operator (if exists)
        return Operator::where('user_id', $request->user()->id)->first();
    }

    /**
     * GET /api/v1/operator/bookings
     * Operator views all incoming bookings for their destinations.
     */
    public function index(Request $request)
    {
        $request->validate([
            'operator_id' => 'nullable|string',
            'status'      => 'nullable|string',
        ]);

        $query = Booking::with(['destination', 'user']);

        if ($request->has('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        } else {
            // Fall back: bookings linked to destinations owned by this user's operator
            $operator = $this->getOperator($request);
            if ($operator) {
                $destIds = $operator->destinations()->pluck('id');
                $query->whereIn('destination_id', $destIds);
            } else {
                return response()->json(['success' => true, 'data' => []]);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->get()->map(fn($b) => $this->formatBooking($b));

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    /**
     * PATCH /api/v1/operator/bookings/{id}/confirm
     */
    public function confirm(Request $request, string $id)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        $booking = Booking::findOrFail($id);
        $booking->update([
            'status'         => 'confirmed',
            'operator_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed.',
            'data'    => $this->formatBooking($booking->fresh(['destination', 'user'])),
        ]);
    }

    /**
     * PATCH /api/v1/operator/bookings/{id}/reject
     */
    public function reject(Request $request, string $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $booking = Booking::findOrFail($id);
        $booking->update([
            'status'           => 'cancelled',
            'cancelled_reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking rejected.',
        ]);
    }

    private function formatBooking(Booking $b): array
    {
        $dest = $b->destination;
        $user = $b->user;
        return [
            'id'                 => $b->id,
            'confirmation_code'  => $b->confirmation_code,
            'status'             => $b->status,
            'payment_status'     => $b->payment_status,
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
            'traveler' => $user ? [
                'id'        => $user->id,
                'name'      => $user->display_name ?? trim("{$user->first_name} {$user->last_name}"),
                'email'     => $user->email,
                'phone'     => $user->phone,
                'photo_url' => $user->photo_url,
            ] : null,
            'destination' => $dest ? [
                'id'    => $dest->id,
                'title' => $dest->title,
            ] : null,
        ];
    }
}
