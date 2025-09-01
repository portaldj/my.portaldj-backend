<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserProfileController extends Controller
{
    public function __construct(protected UserProfileService $userProfileService)
    {
    }

    public function show(User $user): UserResource
    {
        $user->load('city.country', 'profileTypes', 'skills', 'musicGenres', 'profileImage');
        return new UserResource($user);
    }

    public function update(UpdateUserProfileRequest $request, User $user): UserResource
    {
        Gate::authorize('update', $user);

        $updatedUser = $this->userProfileService->updateProfile(
            $user,
            $request->safe()->except('profile_image'),
            $request->file('profile_image')
        );

        return new UserResource($updatedUser);
    }
}
