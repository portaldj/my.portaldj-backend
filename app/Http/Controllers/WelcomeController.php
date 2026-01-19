<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('DJ')->with(['profile.city.country', 'profile.djType']);

        // Search by Name or Username
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($pq) use ($search) {
                        $pq->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Country
        if ($request->filled('country_id')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('country_id', $request->input('country_id'));
            });
        }

        // Filter by City
        if ($request->filled('city_id')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('city_id', $request->input('city_id'));
            });
        }

        // Get Latest DJs
        $latestDJs = $query->latest()->take(9)->get();

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'latestDJs' => $latestDJs,
            'filters' => [
                'countries' => Country::with('cities')->get(['id', 'name']),
            ],
            'request' => $request->only(['search', 'country_id', 'city_id']),
        ]);
    }
}
