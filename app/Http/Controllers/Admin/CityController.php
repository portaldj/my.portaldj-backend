<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
        ]);

        City::create($validated);

        return redirect()->back()->with('success', 'City added successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->back()->with('success', 'City deleted successfully.');
    }
}
