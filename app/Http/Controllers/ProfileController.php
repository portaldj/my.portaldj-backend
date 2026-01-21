<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\ProfileService;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(string $username)
    {
        $user = User::where(function ($q) use ($username) {
            $q->where('id', $username)
                ->orWhereHas('profile', function ($query) use ($username) {
                    $query->where('username', $username);
                });
        })->with([
                    'profile.city.country',
                    'profile.djType',
                    'socialNetworks.platform',
                    'experiences',
                    'equipment' => function ($query) {
                        $query->where('is_public', true)->with(['brand', 'type', 'equipmentModel.brand', 'equipmentModel.type']);
                    },
                    'genres',
                    'roles'
                ])->firstOrFail();

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'isOwnProfile' => auth()->id() === $user->id,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'countries' => \App\Models\Country::active()->with(['cities' => fn($q) => $q->active()])->get(),
            'djTypes' => \App\Models\DjType::all(),
            'socialPlatforms' => \App\Models\SocialPlatform::all(),
            'clubs' => \App\Models\Club::select('id', 'name')->orderBy('name')->get(),
            'brands' => \App\Models\Brand::select('id', 'name')->orderBy('name')->get(),
            'equipmentTypes' => \App\Models\EquipmentType::select('id', 'name')->orderBy('name')->get(),
            'user' => $request->user()->fresh()->load('profile', 'socialNetworks.platform', 'experiences', 'equipment.brand', 'equipment.type', 'equipment.equipmentModel.brand', 'equipment.equipmentModel.type'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // We use a custom validation here or rely on the service validation if moved there.
        // For simplicity, we can validate here using similar rules as Api\ProfileController
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique(User::class)->ignore($request->user()->id)],

            // Add other fields
            'username' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('profiles')->ignore($request->user()->profile->id ?? null)],
            'phone' => ['nullable', 'string', 'max:20'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'dj_type_id' => ['nullable', 'exists:dj_types,id'],
            'biography' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'max:5120'], // 5MB Max
            'social_networks' => ['nullable', 'array'],
            'social_networks.*.social_platform_id' => ['required_with:social_networks', 'exists:social_platforms,id'],
            'social_networks.*.url' => ['required_with:social_networks', 'string'],

            'experiences' => ['nullable', 'array'],
            'experiences.*.title' => ['required_with:experiences', 'string', 'max:255'],
            'experiences.*.place' => ['required_with:experiences', 'string', 'max:255'],
            'experiences.*.description' => ['nullable', 'string'],
            'experiences.*.start_date' => ['required_with:experiences', 'date'],
            'experiences.*.end_date' => ['nullable', 'date', 'after_or_equal:experiences.*.start_date'],

            'openai_key' => ['nullable', 'string'],
            'gemini_key' => ['nullable', 'string'],
            'locale' => ['nullable', 'string', 'in:en,es'],
        ]);

        $request->user()->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'openai_key' => $validated['openai_key'] ?? $request->user()->openai_key,
            'gemini_key' => $validated['gemini_key'] ?? $request->user()->gemini_key,
            'locale' => $validated['locale'] ?? $request->user()->locale,
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Security: Allow users to update their username (Validation handles uniqueness)
        // if (!$request->user()->hasRole('Admin')) {
        //    unset($validated['username']);
        // }

        // Update Profile via Service
        $this->profileService->updateProfile($request->user(), $validated);

        return Redirect::route('profile.edit');

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
