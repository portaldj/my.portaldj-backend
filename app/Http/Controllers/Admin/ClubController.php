<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:club,event_center',
            'address' => 'nullable|string|max:255',
        ]);

        Club::create($validated);

        return redirect()->back()->with('success', 'Club added successfully.');
    }

    public function destroy(Club $club)
    {
        $club->delete();

        return redirect()->back()->with('success', 'Club deleted successfully.');
    }
}
