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

        // Visibility Filter
        $query->where(function ($q) {
            $q->whereNull('visible_from')->orWhere('visible_from', '<=', now());
        })->where(function ($q) {
            $q->whereNull('visible_until')->orWhere('visible_until', '>=', now());
        });

        $query->withCount([
            'downloads as user_downloads_count' => function ($q) {
                $q->where('user_id', auth()->id());
            }
        ]);

        $paginator = $query->paginate($perPage);

        // Append limit to collection (or share via props globally, but simpler to just know the limit)
        // We can't easily append to paginator meta without transforming. 
        // Let's rely on sharing the 'limit' via Inertia in web.php or hardcoding/fetching.
        // Better: Share 'limit' in the Inertia response in web.php.

        return $paginator;
    }

    public function downloadSong(User $user, int $songId)
    {
        $song = Song::findOrFail($songId);

        // Check visibility
        if (
            ($song->visible_from && $song->visible_from > now()) ||
            ($song->visible_until && $song->visible_until < now())
        ) {
            abort(404, 'Song not available.');
        }

        // Check subscription logic here (e.g. if user is PRO)
        if (!$user->can('download songs') && $song->is_pro_only) {
            abort(403, 'Subscription required to download PRO songs.');
        }

        // Check limits
        // Use Song specific limit (default 2 from DB)
        $limit = $song->download_limit;

        $downloadCount = $user->downloads()->where('song_id', $songId)->count();
        if ($downloadCount >= $limit) {
            abort(403, 'Download limit reached for this track.');
        }

        // Record download
        $user->downloads()->create(['song_id' => $songId]);

        // Return valid song
        return $song;
    }
}
