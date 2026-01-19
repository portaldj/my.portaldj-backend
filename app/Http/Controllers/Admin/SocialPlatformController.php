<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialPlatform;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SocialPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/SocialPlatforms/Index', [
            'platforms' => SocialPlatform::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/SocialPlatforms/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:social_platforms',
            'base_url' => 'nullable|string|max:255',
            'icon' => 'nullable|string', // SVG code
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        SocialPlatform::create($validated);

        return redirect()->route('admin.social-platforms.index')->with('success', 'Platform created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialPlatform $socialPlatform)
    {
        return Inertia::render('Admin/SocialPlatforms/Edit', [
            'platform' => $socialPlatform,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialPlatform $socialPlatform)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:social_platforms,name,' . $socialPlatform->id,
            'base_url' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $socialPlatform->update($validated);

        return redirect()->route('admin.social-platforms.index')->with('success', 'Platform updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialPlatform $socialPlatform)
    {
        $socialPlatform->delete();

        return redirect()->back()->with('success', 'Platform deleted successfully.');
    }
}
