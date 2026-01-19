<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries',
            'code' => 'required|string|max:3|uppercase|unique:countries',
        ]);

        Country::create($validated);

        return redirect()->back()->with('success', 'Country added successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->back()->with('success', 'Country deleted successfully.');
    }
}
