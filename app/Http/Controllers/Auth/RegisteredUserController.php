<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'countries' => \App\Models\Country::active()->with(['cities' => fn($q) => $q->active()])->get(),
            'djTypes' => \App\Models\DjType::all(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'username' => ['required', 'string', 'max:255', 'unique:profiles', 'regex:/^[a-zA-Z0-9._]+$/'],
            'phone' => 'required|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'dj_type_id' => 'required|exists:dj_types,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'locale' => app()->getLocale(), // Save current locale
        ]);

        // Create Profile
        $user->profile()->create([
            'username' => $request->username,
            'first_name' => explode(' ', $request->name)[0], // Simple fallback
            'last_name' => explode(' ', $request->name)[1] ?? '',
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'dj_type_id' => $request->dj_type_id,
        ]);

        // Assign Role (Default to DJ for public registration based on prompt scope)
        $user->assignRole('DJ');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))->with('success', __('Registration successful! Welcome to Portal DJ.'));
    }
}
