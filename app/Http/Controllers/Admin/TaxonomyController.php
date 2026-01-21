<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Country;
use App\Models\City;
use App\Models\Club;
use App\Models\DjType;

class TaxonomyController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Taxonomy/Index', [
            'countries' => Country::all(),
            'cities' => City::with('country')->get(),
            'clubs' => Club::with('city.country')->get(),
            'djTypes' => DjType::all(),
        ]);
    }
    public function toggleCountry(Country $country)
    {
        $country->update(['is_active' => !$country->is_active]);
        return back()->with('success', 'Country status updated.');
    }

    public function toggleCity(City $city)
    {
        $city->update(['is_active' => !$city->is_active]);
        return back()->with('success', 'City status updated.');
    }
}
