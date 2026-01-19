<?php

namespace App\Services;

use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PoolService
{
    public function searchSongs(array $filters, int $perPage = 20)
    {
        $query = Song::query()->with('genre');

        if (!empty($filters['query'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('artist_name', 'like', "%{$filters['query']}%")
                    ->orWhere('track_name', 'like', "%{$filters['query']}%");
            });
        }

        if (!empty($filters['genre_id'])) {
            $query->where('genre_id', $filters['genre_id']);
        }

        if (!empty($filters['bpm_min'])) {
            $query->where('bpm', '>=', $filters['bpm_min']);
        }

        if (!empty($filters['bpm_max'])) {
            $query->where('bpm', '<=', $filters['bpm_max']);
        }

        return $query->paginate($perPage);
    }

    public function downloadSong(User $user, int $songId)
    {
        $song = Song::findOrFail($songId);

        // Check subscription logic here (e.g. if user is PRO)
        if (!$user->can('download songs') && $song->is_pro_only) {
            abort(403, 'Subscription required to download PRO songs.');
        }

        // Check limits (2 per song per user)
        $downloadCount = $user->downloads()->where('song_id', $songId)->count();
        if ($downloadCount >= 2) {
            abort(403, 'Download limit reached for this track.');
        }

        // Record download
        $user->downloads()->create(['song_id' => $songId]);

        // Return path for download response
        return Storage::path($song->file_path); // Or return direct URL if CDN
    }
}
