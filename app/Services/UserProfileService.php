<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    /**
     * @param User $user
     * @param array $data
     * @param UploadedFile|null $profileImage
     * @return User
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $profileImage): User
    {
        return DB::transaction(function () use ($user, $data, $profileImage) {
            $user->update($data);

            if (isset($data['skills'])) {
                $user->skills()->sync($data['skills']);
            }
            if (isset($data['music_genres'])) {
                $user->musicGenres()->sync($data['music_genres']);
            }
            if (isset($data['profile_types'])) {
                $user->profileTypes()->sync($data['profile_types']);
            }

            if ($profileImage) {
                if ($user->profileImage) {
                    Storage::disk('public')->delete($user->profileImage->path);
                    $user->profileImage->delete();
                }

                $path = $profileImage->store('profiles', 'public');
                $user->profileImage()->create(['path' => $path, 'type' => 'profile']);
            }

            return $user->fresh();
        });
    }
}
