<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        return $request->user()->load(['profile', 'socialNetworks', 'experiences']);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('profiles')->ignore($request->user()->profile->id ?? null)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'biography' => ['nullable', 'string'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'dj_type_id' => ['nullable', 'exists:dj_types,id'],
            'profile_image' => ['nullable', 'image', 'max:2048'],

            // Nested arrays for relations
            'social_networks' => ['nullable', 'array'],
            'social_networks.*.platform' => ['required', 'string'],
            'social_networks.*.url' => ['required', 'url'],
            'social_networks.*.order' => ['nullable', 'integer'],

            'experiences' => ['nullable', 'array'],
            'experiences.*.title' => ['required', 'string'],
            'experiences.*.place' => ['required', 'string'],
            'experiences.*.start_date' => ['required', 'date'],
            'experiences.*.end_date' => ['nullable', 'date'],
        ]);

        // Security: Prevent non-admins from updating their username
        if (!$request->user()->hasRole('Admin')) {
            unset($validated['username']);
        }

        $user = $this->profileService->updateProfile($request->user(), $validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}
