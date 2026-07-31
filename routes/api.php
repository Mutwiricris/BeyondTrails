<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PhoneAuthController;
use App\Http\Controllers\Api\V1\Auth\SocialAuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Discover\DiscoverController;
use App\Http\Controllers\Api\V1\Discover\GemController;
use App\Http\Controllers\Api\V1\Discover\OperatorController;
use App\Http\Controllers\Api\V1\Discover\DiscoverRouteController;
use App\Http\Controllers\Api\V1\Discover\PeopleNearbyController;
use App\Http\Controllers\Api\V1\Discover\StoryController;
use App\Http\Controllers\Api\V1\Discover\ChallengeController;
use App\Http\Controllers\Api\V1\Discover\LocationHierarchyController;
use App\Http\Controllers\Api\V1\Discover\GeofenceController;
use App\Http\Controllers\Api\V1\Discover\ActivityController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\MapLocationController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\Discover\OperatorBookingController;

// ── Root API Index & Health status ──────────────────────────────────────────
Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'BeyondTrails API v1 is online and operational',
        'version' => '1.0.0',
        'endpoints' => [
            'destinations' => url('/api/v1/discover/destinations'),
            'activities' => url('/api/v1/discover/activities'),
            'gems' => url('/api/v1/discover/gems'),
            'operators' => url('/api/v1/discover/operators'),
            'routes' => url('/api/v1/discover/routes'),
            'auth' => url('/api/v1/auth/login'),
        ],
    ]);
});

// ── Public media proxy (no auth) — serves stored files with CORS headers ──────
// This bypasses PHP's static-file serving which strips Laravel CORS middleware.
Route::get('v1/media/{path}', [MediaController::class, 'serve'])
    ->where('path', '.*')
    ->name('media.serve');


/*
|--------------------------------------------------------------------------
| API Routes — ZuriTrails v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth Routes (Public — no token required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {

        // ──────────────────────────────────────────────────────────────────
        // Email Registration
        // POST /api/v1/auth/check-email
        // POST /api/v1/auth/register
        // POST /api/v1/auth/register/multi-step
        // ──────────────────────────────────────────────────────────────────
        Route::post('/check-email', [AuthController::class, 'checkEmail'])
            ->name('auth.check_email');

        Route::post('/register', [AuthController::class, 'register'])
            ->name('auth.register');

        Route::post('/register/multi-step', [AuthController::class, 'registerMultiStep'])
            ->name('auth.register.multistep');

        Route::post('/username-suggestions', [AuthController::class, 'usernameSuggestions'])
            ->name('auth.username_suggestions');

        Route::get('/signup-options', [\App\Http\Controllers\OptionsController::class, 'index'])
            ->name('auth.signup_options');

        // ──────────────────────────────────────────────────────────────────
        // Email Login
        // POST /api/v1/auth/login
        // ──────────────────────────────────────────────────────────────────
        Route::post('/login', [AuthController::class, 'login'])
            ->name('auth.login');

        // ──────────────────────────────────────────────────────────────────
        // Phone OTP Auth
        // POST /api/v1/auth/phone/request-otp
        // POST /api/v1/auth/phone/verify-otp
        // POST /api/v1/auth/phone/resend-otp
        // ──────────────────────────────────────────────────────────────────
        Route::prefix('phone')->group(function () {
            Route::post('/request-otp', [PhoneAuthController::class, 'requestOtp'])
                ->name('auth.phone.request');

            Route::post('/verify-otp', [PhoneAuthController::class, 'verifyOtp'])
                ->name('auth.phone.verify');

            Route::post('/resend-otp', [PhoneAuthController::class, 'resendOtp'])
                ->name('auth.phone.resend');
        });

        // ──────────────────────────────────────────────────────────────────
        // Social Login (Google, Apple, Facebook)
        // POST /api/v1/auth/social
        // ──────────────────────────────────────────────────────────────────
        Route::post('/social', [SocialAuthController::class, 'login'])
            ->name('auth.social');

        // ──────────────────────────────────────────────────────────────────
        // Password Management
        // POST /api/v1/auth/password/forgot
        // POST /api/v1/auth/password/reset
        // ──────────────────────────────────────────────────────────────────
        Route::prefix('password')->group(function () {
            Route::post('/forgot', [PasswordController::class, 'forgot'])
                ->name('auth.password.forgot');

            Route::post('/reset', [PasswordController::class, 'reset'])
                ->name('auth.password.reset');
        });

        // ──────────────────────────────────────────────────────────────────
        // Email Verification (public link clicked from email)
        // POST /api/v1/auth/email/verify
        // ──────────────────────────────────────────────────────────────────
        Route::prefix('email')->group(function () {
            Route::post('/verify', [EmailVerificationController::class, 'verify'])
                ->name('auth.email.verify');
        });

        // ──────────────────────────────────────────────────────────────────
        // Protected Auth Routes (Bearer token required)
        // ──────────────────────────────────────────────────────────────────
        Route::middleware('auth:sanctum')->group(function () {

            // Current user
            Route::get('/me', [AuthController::class, 'me'])
                ->name('auth.me');

            // Logout
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('auth.logout');

            Route::post('/logout-all', [AuthController::class, 'logoutAll'])
                ->name('auth.logout.all');

            // Token refresh
            Route::post('/refresh', [AuthController::class, 'refresh'])
                ->name('auth.refresh');

            // Change password (authenticated)
            Route::post('/password/change', [PasswordController::class, 'change'])
                ->name('auth.password.change');

            // Resend email verification
            Route::post('/email/send-verification', [EmailVerificationController::class, 'send'])
                ->name('auth.email.send');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Application Routes (Bearer token required)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        // Profile routes
        Route::prefix('users')->group(function () {
            Route::get('/profile', [\App\Http\Controllers\Api\V1\User\ProfileController::class, 'show'])->name('auth.profile.show');
            Route::put('/profile', [\App\Http\Controllers\Api\V1\User\ProfileController::class, 'update'])->name('auth.profile.update');
        });

        // Chat routes
        Route::prefix('chat')->group(function () {
            Route::get('/conversations', [\App\Http\Controllers\Api\V1\ChatController::class, 'index'])->name('chat.conversations');
            Route::get('/{user_id}', [\App\Http\Controllers\Api\V1\ChatController::class, 'show'])->name('chat.show');
            Route::post('/{user_id}', [\App\Http\Controllers\Api\V1\ChatController::class, 'store'])->name('chat.store');
        });

        // ── Booking Routes (User) ────────────────────────────────────────────
        Route::prefix('bookings')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
            Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
            Route::get('/{id}', [BookingController::class, 'show'])->name('bookings.show');
            Route::delete('/{id}', [BookingController::class, 'cancel'])->name('bookings.cancel');
        });

        // ── Operator Booking Management Routes ───────────────────────────────
        Route::prefix('operator')->group(function () {
            Route::get('/bookings', [OperatorBookingController::class, 'index'])->name('operator.bookings.index');
            Route::patch('/bookings/{id}/confirm', [OperatorBookingController::class, 'confirm'])->name('operator.bookings.confirm');
            Route::patch('/bookings/{id}/reject', [OperatorBookingController::class, 'reject'])->name('operator.bookings.reject');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Discover Routes — Public (no auth required)
    | Browsing destinations, gems, operators, routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('discover')->group(function () {

        // ── Search (cross-entity) ────────────────────────────────────────────
        Route::get('/search', [DiscoverController::class, 'search'])
            ->name('discover.search');

        // ── People Nearby ────────────────────────────────────────────────────
        Route::get('/people-nearby', PeopleNearbyController::class)
            ->name('discover.people-nearby');

        // ── Location Hierarchy ───────────────────────────────────────────────
        Route::get('/location-nodes', [LocationHierarchyController::class, 'index'])
            ->name('discover.location-nodes.index');
        Route::get('/location-nodes/{id}', [LocationHierarchyController::class, 'show'])
            ->name('discover.location-nodes.show');

        // ── Geofences ────────────────────────────────────────────────────────
        Route::get('/geofences', [GeofenceController::class, 'index'])
            ->name('discover.geofences.index');
        Route::post('/geofences/trigger', [GeofenceController::class, 'trigger'])
            ->name('discover.geofences.trigger');

        // ── Map Locations ────────────────────────────────────────────────────
        Route::get('/map-locations', [MapLocationController::class, 'index'])
            ->name('discover.map-locations.index');

        // ── Activities ───────────────────────────────────────────────────────
        Route::prefix('activities')->group(function () {
            Route::get('/', [ActivityController::class, 'index'])->name('discover.activities.index');
            Route::get('/{id}', [ActivityController::class, 'show'])->name('discover.activities.show');
            Route::get('/{id}/messages', [ActivityController::class, 'getMessages'])->name('discover.activities.messages.get');
        });

        // ── Stories ──────────────────────────────────────────────────────────
        Route::get('/stories', [StoryController::class, 'index'])
            ->name('discover.stories');

        // ── Challenges ───────────────────────────────────────────────────────
        Route::get('/challenges', [ChallengeController::class, 'index'])
            ->name('discover.challenges');

        // ── Destinations ─────────────────────────────────────────────────────
        Route::prefix('destinations')->group(function () {
            Route::get('/',          [DiscoverController::class, 'indexDestinations'])->name('discover.destinations.index');
            Route::get('/featured',  [DiscoverController::class, 'featuredDestinations'])->name('discover.destinations.featured');
            Route::get('/{slug}',    [DiscoverController::class, 'showDestination'])->name('discover.destinations.show');
            Route::get('/{slug}/similar', [DiscoverController::class, 'similarDestinations'])->name('discover.destinations.similar');
            Route::get('/{id}/reviews', function ($id) {
                // Public reviews for a destination
                $reviews = \App\Models\Review::with('user')
                    ->where('reviewable_type', 'destination')
                    ->where('reviewable_id', $id)
                    ->latest()->paginate(20);
                return response()->json(['success' => true, 'data' => $reviews]);
            })->name('discover.destinations.reviews');
            Route::get('/{id}/availability', [BookingController::class, 'checkAvailability'])->name('discover.destinations.availability');
        });

        // ── Hidden Gems ───────────────────────────────────────────────────────
        Route::prefix('gems')->group(function () {
            Route::get('/',                     [GemController::class, 'index'])->name('discover.gems.index');
            Route::get('/{slug}',               [GemController::class, 'show'])->name('discover.gems.show');
            Route::get('/{id}/nearby',          [GemController::class, 'nearby'])->name('discover.gems.nearby');
            Route::get('/{id}/weather',         [GemController::class, 'weather'])->name('discover.gems.weather');
            Route::get('/{id}/travellers-nearby', [GemController::class, 'travellersNearby'])->name('discover.gems.travellers');
        });

        // ── Operators ─────────────────────────────────────────────────────────
        Route::prefix('operators')->group(function () {
            Route::get('/',           [OperatorController::class, 'index'])->name('discover.operators.index');
            Route::get('/{slug}',     [OperatorController::class, 'show'])->name('discover.operators.show');
            Route::get('/{id}/tours', [OperatorController::class, 'tours'])->name('discover.operators.tours');
        });

        // ── Routes ────────────────────────────────────────────────────────────
        Route::prefix('routes')->group(function () {
            Route::get('/',       [DiscoverRouteController::class, 'index'])->name('discover.routes.index');
            Route::get('/{slug}', [DiscoverRouteController::class, 'show'])->name('discover.routes.show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Discover Auth Routes — Protected (token required)
    | Submitting reviews, wishlists, new gems
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->prefix('discover')->group(function () {

        // Submit reviews
        Route::post('/destinations/{id}/reviews', [DiscoverController::class, 'storeDestinationReview'])
            ->name('discover.destinations.reviews.store');
        Route::post('/gems/{id}/reviews', [GemController::class, 'storeReview'])
            ->name('discover.gems.reviews.store');

        // Submit new hidden gem
        Route::post('/gems', [GemController::class, 'store'])
            ->name('discover.gems.store');

        // Activities (Protected)
        Route::prefix('activities')->group(function () {
            Route::post('/', [ActivityController::class, 'store'])->name('discover.activities.store');
            Route::post('/{id}/join', [ActivityController::class, 'join'])->name('discover.activities.join');
            Route::post('/{id}/leave', [ActivityController::class, 'leave'])->name('discover.activities.leave');
            Route::post('/{id}/report', [ActivityController::class, 'report'])->name('discover.activities.report');
            Route::post('/{id}/messages', [ActivityController::class, 'sendMessage'])->name('discover.activities.messages.send');
        });
    });
});

