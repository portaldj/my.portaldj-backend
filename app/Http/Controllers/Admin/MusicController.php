<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\MusicGenre;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Music/Index', [
            'songs' => Song::with('genre')->latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Music/Create', [
            'genres' => MusicGenre::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'track_name' => 'required|string|max:255',
            'artist_name' => 'required|string|max:255',
            'bpm' => 'required|integer|min:50|max:200',
            'key' => 'nullable|string|max:10',
            'remix_type' => 'nullable|string|max:50',
            'genre_id' => 'required|exists:music_genres,id',
            'is_pro_only' => 'boolean',
            'visible_from' => 'nullable|date',
            'visible_until' => 'nullable|date|after_or_equal:visible_from',
            'download_limit' => 'required|integer|min:1',
            'file' => 'required|file|mimes:mp3,wav|max:20480', // 20MB max
            'download_url' => 'nullable|url|max:255',
            'preview_file' => 'nullable|file|mimes:mp3,wav|max:10240', // 10MB max for preview
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('songs', 'r2-private');
            if (!$path) {
                throw new \Exception('Failed to upload file to R2 Private');
            }
            $validated['file_path'] = $path;
        }

        if ($request->hasFile('preview_file')) {
            $path = $request->file('preview_file')->store('songs/previews', 'r2-private');
            $validated['preview_file_path'] = $path;
        }

        Song::create($validated);

        return redirect()->route('admin.music.index')->with('success', 'Song uploaded successfully.');
    }

    public function edit(Song $song)
    {
        return Inertia::render('Admin/Music/Edit', [
            'song' => $song,
            'genres' => MusicGenre::all()
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $validated = $request->validate([
            'track_name' => 'required|string|max:255',
            'artist_name' => 'required|string|max:255',
            'bpm' => 'required|integer|min:50|max:200',
            'key' => 'nullable|string|max:10',
            'remix_type' => 'nullable|string|max:50',
            'genre_id' => 'required|exists:music_genres,id',
            'is_pro_only' => 'boolean',
            'visible_from' => 'nullable|date',
            'visible_until' => 'nullable|date|after_or_equal:visible_from',
            'download_limit' => 'required|integer|min:1',
            'file' => 'nullable|file|mimes:mp3,wav|max:20480', // 20MB max
            'download_url' => 'nullable|url|max:255',
            'preview_file' => 'nullable|file|mimes:mp3,wav|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            // Delete old file (Check R2 then Public)
            if ($song->file_path) {
                if (Storage::disk('r2-private')->exists($song->file_path)) {
                    Storage::disk('r2-private')->delete($song->file_path);
                } elseif (Storage::disk('public')->exists($song->file_path)) {
                    Storage::disk('public')->delete($song->file_path);
                }
            }
            // Upload new file to Private R2
            $path = $request->file('file')->store('songs', 'r2-private');
            $validated['file_path'] = $path;
        }

        if ($request->hasFile('preview_file')) {
            // Delete old preview (Check R2 then Public)
            if ($song->preview_file_path) {
                if (Storage::disk('r2-private')->exists($song->preview_file_path)) {
                    Storage::disk('r2-private')->delete($song->preview_file_path);
                } elseif (Storage::disk('public')->exists($song->preview_file_path)) {
                    Storage::disk('public')->delete($song->preview_file_path);
                }
            }
            // Upload new preview to Private R2
            $path = $request->file('preview_file')->store('songs/previews', 'r2-private');
            $validated['preview_file_path'] = $path;
        }

        $song->update($validated);

        return redirect()->route('admin.music.index')->with('success', 'Song updated successfully.');
    }

    public function destroy(Song $song)
    {
        // Delete file from storage
        // Delete file from storage (Check R2 then Public)
        if ($song->file_path) {
            if (Storage::disk('r2-private')->exists($song->file_path)) {
                Storage::disk('r2-private')->delete($song->file_path);
            } elseif (Storage::disk('public')->exists($song->file_path)) {
                Storage::disk('public')->delete($song->file_path);
            }
        }

        // Delete preview
        if ($song->preview_file_path) {
            if (Storage::disk('r2-private')->exists($song->preview_file_path)) {
                Storage::disk('r2-private')->delete($song->preview_file_path);
            } elseif (Storage::disk('public')->exists($song->preview_file_path)) {
                Storage::disk('public')->delete($song->preview_file_path);
            }
        }

        $song->delete();

        return redirect()->route('admin.music.index')->with('success', 'Song deleted.');
    }
}
