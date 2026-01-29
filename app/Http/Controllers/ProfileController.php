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
            'events' => $user->events()->where('is_public', true)->get()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'description' => $event->description,
                    'url' => $event->url,
                    'className' => 'bg-brand-primary border-brand-primary text-white',
                    'extendedProps' => [
                        'description' => $event->description,
                        'url' => $event->url,
                    ]
                ];
            }),
            'upcomingEvents' => $user->events()
                ->where('is_public', true)
                ->where('start', '>=', now())
                ->orderBy('start')
                ->take(3)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $event->start,
                        'end' => $event->end,
                        'description' => $event->description,
                        'url' => $event->url,
                    ];
                }),
        ]);
    }

    /**
     * Display the user's profile form.
     */
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
            'allGenres' => \App\Models\MusicGenre::select('id', 'name')->orderBy('name')->get(),
            'socialPlatforms' => \App\Models\SocialPlatform::all(),
            'clubs' => \App\Models\Club::select('id', 'name')->orderBy('name')->get(),
            'brands' => \App\Models\Brand::select('id', 'name')->orderBy('name')->get(),
            'equipmentTypes' => \App\Models\EquipmentType::select('id', 'name')->orderBy('name')->get(),
            'user' => $request->user()->fresh()->load('profile', 'socialNetworks.platform', 'experiences', 'equipment.brand', 'equipment.type', 'equipment.equipmentModel.brand', 'equipment.equipmentModel.type', 'genres'),
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique(User::class)->ignore($request->user()->id)],

            // Add other fields
            'username' => ['sometimes', 'required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('profiles')->ignore($request->user()->profile->id ?? null), 'regex:/^[a-zA-Z0-9._]+$/'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_email_public' => ['boolean'],
            'is_phone_public' => ['boolean'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:music_genres,id'],
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
            'theme' => ['nullable', 'string', 'in:light,dark,system'],
        ]);

        if (isset($validated['name'])) {
            $request->user()->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $request->user()->email = $validated['email'];
        }

        $request->user()->fill([
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

        return Redirect::route('profile.edit')->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update the user's theme preference.
     */
    public function updateTheme(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
        ]);

        $user = $request->user();

        if ($user->profile) {
            $user->profile->update(['theme' => $validated['theme']]);
        }

        return back();
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
