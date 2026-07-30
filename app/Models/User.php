<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The primary key type.
     */
    

    /**
     * The attributes that are mass assignable.
     *
     * Exactly matching the fields from:
     * - signup_screen.dart (simple signup)
     * - SignupData model (multi-step)
     * - UserProfile model (full profile)
     */
    protected $fillable = [
        // Auth
        'email',
        'phone_number',
        'password',

        // Basic Profile
        'name',
        'username',
        'first_name',
        'last_name',
        'display_name',
        'date_of_birth',

        // Extended Profile
        'bio',
        'photo_url',
        'photo_thumbnail_url',
        'gender',
        'nationality',
        'home_country',
        'referral_source',

        // Documents
        'id_number',
        'passport_number',
        
        // Explorer Location Sharing
        'sharing_mode',
        'traveller_status',
        'explorer_level',
        'streak_days',
        'last_seen_at',
        'allow_dms',
        'gems_discovered_count',

        // Settings
        'currency_preference',
        'language_preference',

        // Address
        'address',
        'city',
        'country',
        'postal_code',

        // Preferences (JSONB)
        'interests',
        'languages',
        'travel_styles',
        'activity_preferences',
        'dietary_restrictions',
        'accessibility_needs',

        // Settings
        'preferred_currency',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'location_enabled',
        'show_distance_away',

        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',

        // Travel Preferences
        'travel_style',
        'travel_insurance',

        // Gamification
        'explorer_level',
        'current_xp',
        'streak_days',
        'unlocked_badges',

        // Account
        'role',
        'profile_completion',
        'is_profile_public',
        'share_location_with_friends',

        // Verification
        'email_verified_at',
        'phone_verified_at',

        // Metadata
        'last_active_at',

        // Selectables
        'selectables',

        // Coordinates & Activity
        'latitude',
        'longitude',
        'current_activity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth'             => 'date',
        'email_verified_at'         => 'datetime',
        'phone_verified_at'         => 'datetime',
        'last_active_at'            => 'datetime',

        // Booleans
        'email_notifications'       => 'boolean',
        'sms_notifications'         => 'boolean',
        'push_notifications'        => 'boolean',
        'location_enabled'          => 'boolean',
        'show_distance_away'        => 'boolean',
        'travel_insurance'          => 'boolean',
        'is_profile_public'         => 'boolean',
        'share_location_with_friends' => 'boolean',

        // Integers
        'current_xp'                => 'integer',
        'streak_days'               => 'integer',
        'profile_completion'        => 'integer',

        // JSONB arrays
        'interests'                 => 'array',
        'languages'                 => 'array',
        'travel_styles'             => 'array',
        'activity_preferences'      => 'array',
        'dietary_restrictions'      => 'array',
        'accessibility_needs'       => 'array',
        'unlocked_badges'           => 'array',
        'selectables'               => 'array',
        'latitude'                  => 'float',
        'longitude'                 => 'float',
    ];

    // ───────────────────────────────────────────────────────────────────────
    // Relationships
    // ───────────────────────────────────────────────────────────────────────

    public function socialProviders()
    {
        return $this->hasMany(SocialProvider::class);
    }

    public function wishlistedRoutes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_user_wishlist');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function participatingActivities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // ───────────────────────────────────────────────────────────────────────
    // Computed / Accessor Properties
    // ───────────────────────────────────────────────────────────────────────

    /**
     * Get the user's age from date_of_birth.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth
            ? Carbon::parse($this->date_of_birth)->age
            : null;
    }

    /**
     * Get user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Whether email is verified.
     */
    public function getEmailVerifiedAttribute(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Whether phone is verified.
     */
    public function getPhoneVerifiedAttribute(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * XP progress toward next level (0.0 to 1.0).
     * Mirrors Flutter's ExplorerLevel logic.
     */
    public function getLevelProgressAttribute(): float
    {
        $levels = [
            'explorer'    => ['min' => 0,    'max' => 100],
            'pathfinder'  => ['min' => 100,  'max' => 500],
            'trailblazer' => ['min' => 500,  'max' => 1500],
            'pioneer'     => ['min' => 1500, 'max' => 3000],
            'legend'      => ['min' => 3000, 'max' => 5000],
        ];

        $level = $levels[$this->explorer_level] ?? $levels['explorer'];
        $range = $level['max'] - $level['min'];
        if ($range <= 0) return 1.0;

        return min(1.0, max(0.0, ($this->current_xp - $level['min']) / $range));
    }

    /**
     * XP needed to reach next level.
     */
    public function getXpToNextLevelAttribute(): int
    {
        $thresholds = [
            'explorer'    => 100,
            'pathfinder'  => 500,
            'trailblazer' => 1500,
            'pioneer'     => 3000,
            'legend'      => 5000,
        ];

        $required = $thresholds[$this->explorer_level] ?? 100;
        return max(0, $required - $this->current_xp);
    }

    // ───────────────────────────────────────────────────────────────────────
    // Business Logic
    // ───────────────────────────────────────────────────────────────────────

    /**
     * Check if the user meets the minimum age requirement (18+).
     */
    public function isOfLegalAge(): bool
    {
        if (!$this->date_of_birth) return false;
        return Carbon::parse($this->date_of_birth)->age >= 18;
    }

    /**
     * Update the explorer level based on current XP.
     * Called after awarding XP.
     */
    public function recalculateLevel(): void
    {
        $xp = $this->current_xp;

        $newLevel = match (true) {
            $xp >= 3000 => 'legend',
            $xp >= 1500 => 'pioneer',
            $xp >= 500  => 'trailblazer',
            $xp >= 100  => 'pathfinder',
            default     => 'explorer',
        };

        if ($newLevel !== $this->explorer_level) {
            $this->explorer_level = $newLevel;
            $this->save();
        }
    }

    /**
     * Award XP points to the user.
     */
    public function awardXp(int $amount, string $reason = ''): void
    {
        $this->increment('current_xp', $amount);
        $this->recalculateLevel();
    }

    /**
     * Update streak (called on login).
     * - Same day: keep streak
     * - Yesterday: increment streak
     * - Gap > 1 day: reset to 1
     */
    public function updateStreak(): void
    {
        if (!$this->last_active_at) {
            $this->streak_days = 1;
        } else {
            $lastActive = Carbon::parse($this->last_active_at)->startOfDay();
            $today      = Carbon::today();
            $yesterday  = Carbon::yesterday();

            if ($lastActive->equalTo($today)) {
                // Same day — no change
            } elseif ($lastActive->equalTo($yesterday)) {
                // Consecutive day
                $this->streak_days++;
            } else {
                // Streak broken
                $this->streak_days = 1;
            }
        }

        $this->last_active_at = now();
        $this->save();
    }

    /**
     * Calculate and update profile completion percentage.
     * Mirrors Flutter's profileCompletionPercentage getter.
     */
    public function calculateProfileCompletion(): int
    {
        $fields = [
            'email', 'phone_number', 'photo_url', 'bio',
            'date_of_birth', 'nationality', 'address', 'city',
            'country', 'interests', 'languages', 'emergency_contact_name',
            'emergency_contact_phone', 'travel_style', 'preferred_currency',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            $val = $this->$field;
            if (!empty($val)) $filled++;
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    /**
     * Unlock a badge.
     */
    public function unlockBadge(string $badgeId): void
    {
        $badges = $this->unlocked_badges ?? [];
        if (!in_array($badgeId, $badges)) {
            $badges[] = $badgeId;
            $this->unlocked_badges = $badges;
            $this->save();
        }
    }

    /**
     * Generate a unique username based on first and last name.
     */
    public static function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $firstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));
        $lastName  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $lastName));
        
        if (empty($firstName) && empty($lastName)) {
            $firstName = 'explorer';
            $lastName = rand(100, 999);
        }
        
        $base = "{$firstName}{$lastName}";
        if (empty($base)) {
            $base = "explorer_" . rand(100, 999);
        }
        
        $username = $base;
        $count = 1;
        while (self::where('username', $username)->exists()) {
            $username = $base . $count;
            $count++;
        }
        
        return $username;
    }
}
