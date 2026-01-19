<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DjType;
use Illuminate\Http\Request;

class DjTypeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dj_types',
        ]);

        DjType::create($validated);

        return redirect()->back()->with('success', 'DJ Type added successfully.');
    }

    public function destroy(DjType $djType)
    {
        $djType->delete();

        return redirect()->back()->with('success', 'DJ Type deleted successfully.');
    }
}
