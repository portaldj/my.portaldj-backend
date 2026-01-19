<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;
use App\Models\Experience;
use App\Models\SocialNetwork;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update or Create Profile for a User
     */
    public function updateProfile(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // Handle Image Upload
            if (isset($data['profile_image']) && $data['profile_image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['profile_image']->store('profiles', 'public');
                $data['profile_image_path'] = $path;
            }

            // Update Profile Table
            $profileAttributes = [
                'first_name',
                'last_name',
                'phone',
                'birthdate',
                'biography',
                'country_id',
                'city_id',
                'address',
                'dj_type_id',
                'profile_image_path'
            ];

            // Only update username if explicitly provided (and allowed by controller)
            if (isset($data['username'])) {
                $profileAttributes[] = 'username';
            }

            $profileData = collect($data)->only($profileAttributes)->toArray();

            // Fallback for first/last name if creating and not provided
            if (!isset($profileData['first_name'])) {
                $parts = explode(' ', $user->name, 2);
                $profileData['first_name'] = $parts[0];
                $profileData['last_name'] = $parts[1] ?? '';
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            // Sync Social Networks
            // Sync Social Networks
            if (isset($data['social_networks'])) {
                $user->socialNetworks()->delete();
                foreach ($data['social_networks'] as $index => $social) {
                    if (!empty($social['url']) && !empty($social['social_platform_id'])) {
                        $user->socialNetworks()->create([
                            'social_platform_id' => $social['social_platform_id'],
                            'url' => $social['url'],
                            'order' => $index, // Order is preserved by array index from frontend
                        ]);
                    }
                }
            }

            // Sync Experiences
            if (isset($data['experiences'])) {
                // Determine logic: Replace all or update specific. 
                // For simplicity here: Delete and recreate if ID not present, or just recreate.
                // Better approach for detailed forms is syncing. 
                // Here we assume full replacement for the MVP form submission.
                $user->experiences()->delete();
                foreach ($data['experiences'] as $exp) {
                    $user->experiences()->create([
                        'title' => $exp['title'],
                        'place' => $exp['place'],
                        'description' => $exp['description'] ?? null,
                        'start_date' => $exp['start_date'],
                        'end_date' => $exp['end_date'] ?? null,
                    ]);
                }
            }

            return $user->load('profile', 'socialNetworks', 'experiences');
        });
    }
}
