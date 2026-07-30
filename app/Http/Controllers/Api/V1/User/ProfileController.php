<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Add computed properties
        $user->append(['age', 'full_name', 'level_progress', 'xp_to_next_level']);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'bio' => 'nullable|string|max:1000',
            'gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'nationality' => 'nullable|string|max:255',
            'home_country' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            
            // JSON fields
            'interests' => 'nullable|array',
            'languages' => 'nullable|array',
            'travel_styles' => 'nullable|array',
            'activity_preferences' => 'nullable|array',
            'dietary_restrictions' => 'nullable|array',
            'accessibility_needs' => 'nullable|array',

            // Settings
            'preferred_currency' => 'nullable|string|size:3',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'location_enabled' => 'nullable|boolean',
            'show_distance_away' => 'nullable|boolean',

            // Emergency
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            
            // Other
            'travel_style' => 'nullable|string|max:255',
            'travel_insurance' => 'nullable|boolean',
            'is_profile_public' => 'nullable|boolean',
            'share_location_with_friends' => 'nullable|boolean',
            
            // Photo update
            'photo' => 'nullable|image|max:5120', // 5MB max
            'selectables' => 'nullable|array',
        ]);

        // Handle photo upload if present (File upload)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $extension;
            $path = $file->storeAs('profiles', $filename, 'public');
            $validatedData['photo_url'] = Storage::url($path);
            unset($validatedData['photo']);
        }
        
        // Handle base64 image data URL (from mobile JSON API)
        $photoUrl = $request->input('photo_url') ?? $request->input('selectables.photoUrl');
        if ($photoUrl && str_starts_with($photoUrl, 'data:image/')) {
            if (preg_match('/^data:image\/(\w+);base64,/', $photoUrl, $typeMatches)) {
                $imageType = strtolower($typeMatches[1]);
                if (in_array($imageType, ['jpeg', 'jpg', 'png', 'gif', 'webp'])) {
                    $base64Data = substr($photoUrl, strpos($photoUrl, ',') + 1);
                    $decodedData = base64_decode($base64Data);

                    if (strlen($decodedData) <= 5242880) {
                        $filename = 'profile_' . $user->id . '_' . time() . '.' . $imageType;
                        $path = 'profiles/' . $filename;
                        Storage::disk('public')->put($path, $decodedData);
                        
                        $validatedData['photo_url'] = Storage::url($path);
                        
                        if ($request->has('selectables')) {
                            $selectables = $request->input('selectables');
                            $selectables['photoUrl'] = $validatedData['photo_url'];
                            $request->merge(['selectables' => $selectables]);
                        }
                    } else {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'photo' => ['The profile photo size must not exceed 5MB.'],
                        ]);
                    }
                } else {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'photo' => ['The profile photo must be a file of type: jpeg, png, gif, webp.'],
                    ]);
                }
            }
        }

        // Use any provided direct photo_url (for instance, social login links or flutter passing urls)
        if ($request->has('photo_url') && !str_starts_with($request->input('photo_url'), 'data:image/') && !$request->hasFile('photo')) {
            $validatedData['photo_url'] = $request->input('photo_url');
        }

        $oldSelectables = $user->selectables ?? [];

        $user->fill($validatedData);

        if ($request->has('selectables')) {
            $selectables = $request->input('selectables') ?? [];
            $merged = array_merge($oldSelectables, $selectables);
            $user->selectables = $merged;

            // Map selectables to structured columns to keep them synchronized
            $mappings = [
                'firstName'             => 'first_name',
                'lastName'              => 'last_name',
                'photoUrl'              => 'photo_url',
                'bio'                   => 'bio',
                'gender'                => 'gender',
                'nationality'           => 'nationality',
                'dateOfBirth'           => 'date_of_birth',
                'interests'             => 'interests',
                'travelStyles'          => 'travel_styles',
                'activityPreferences'   => 'activity_preferences',
                'homeCountry'           => 'home_country',
                'referralSource'        => 'referral_source',
                'emailNotifications'    => 'email_notifications',
                'smsNotifications'      => 'sms_notifications',
                'pushNotifications'     => 'push_notifications',
                'locationEnabled'       => 'location_enabled',
                'showDistanceAway'      => 'show_distance_away',
                'isProfilePublic'       => 'is_profile_public',
                'shareLocationWithFriends' => 'share_location_with_friends',
                'preferredCurrency'     => 'preferred_currency',
                'emergencyContactName'  => 'emergency_contact_name',
                'emergencyContactPhone' => 'emergency_contact_phone',
                'emergencyContactRelation' => 'emergency_contact_relation',
                'travelStyle'           => 'travel_style',
                'travelInsurance'       => 'travel_insurance',
                'phone'                 => 'phone_number',
                'phoneNumber'           => 'phone_number',
                'city'                  => 'city',
                'country'               => 'country',
                'postalCode'            => 'postal_code',
            ];

            foreach ($mappings as $key => $column) {
                if (array_key_exists($key, $selectables)) {
                    $user->$column = $selectables[$key];
                }
            }
        }

        // Sync user name if first or last name changes
        if ($user->isDirty(['first_name', 'last_name'])) {
            $user->name = trim("{$user->first_name} {$user->last_name}");
        }
        
        // Recalculate profile completion
        $user->profile_completion = $user->calculateProfileCompletion();
        
        $user->save();

        $user->append(['age', 'full_name', 'level_progress', 'xp_to_next_level']);

        $userResource = new \App\Http\Resources\UserResource($user);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user'    => $userResource,
            'data'    => [
                'user' => $userResource,
            ]
        ]);
    }
}
