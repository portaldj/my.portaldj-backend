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
            'file' => 'required|file|mimes:mp3,wav|max:20480', // 20MB max
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('songs', 'public');
            $validated['file_path'] = $path;
        }

        Song::create($validated);

        return redirect()->route('admin.music.index')->with('success', 'Song uploaded successfully.');
    }

    public function destroy(Song $song)
    {
        // Delete file from storage
        if ($song->file_path && Storage::disk('public')->exists($song->file_path)) {
            Storage::disk('public')->delete($song->file_path);
        }

        $song->delete();

        return redirect()->route('admin.music.index')->with('success', 'Song deleted.');
    }
}
