<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongDownloadController extends Controller
{
    public function download(Request $request, Song $song)
    {
        // 1. Check Authentication (handled by middleware usually, but good to be safe)
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = $request->user();

        // 2. Check Permissions
        // If song is pro only, user must be pro
        if ($song->is_pro_only && !$user->is_pro) {
            return redirect()->route('subscription.index')->with('error', 'This track is for Pro members only.');
        }

        // 3. Check Download Limit (Optional, based on logic in other parts of app, but good to track)
        // For now, simple access.

        // 4. Generate Signed URL
        // Check if file exists in R2 Private
        if ($song->file_path && Storage::disk('r2-private')->exists($song->file_path)) {
            $url = Storage::disk('r2-private')->temporaryUrl(
                $song->file_path,
                now()->addMinutes(5), // 5 minutes validity
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $song->file_path . '"', // Optional: simpler
                ]
            );

            // Increment download count
            $song->increment('download_count');
            // Log download for user history
            $user->downloads()->create([
                'song_id' => $song->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect($url);
        }

        // Fallback for Legacy Local Files (if any)
        if ($song->file_path && Storage::disk('public')->exists($song->file_path)) {
            // Local public file... technically this isn't secure if it's in public disk, but for transition:
            $song->increment('download_count');
            return Storage::disk('public')->download($song->file_path);
        }

        abort(404, 'File not found');
    }
    public function preview(Request $request, Song $song)
    {
        // 1. Check Authentication
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        // Preview is usually available to all authenticated users, 
        // regardless of subscription (because they need to hear it to buy/subscribe).
        // If you want to restrict previews to PRO users, add that check here.

        if ($song->preview_file_path && Storage::disk('r2-private')->exists($song->preview_file_path)) {
            $url = Storage::disk('r2-private')->temporaryUrl(
                $song->preview_file_path,
                now()->addMinutes(10), // 10 minutes validity
                [
                    // Open in browser (stream) rather than download
                    'ResponseContentDisposition' => 'inline; filename="preview_' . basename($song->preview_file_path) . '"',
                ]
            );
            return redirect($url);
        }

        // Fallback or 404
        abort(404, 'Preview not found');
    }
}
