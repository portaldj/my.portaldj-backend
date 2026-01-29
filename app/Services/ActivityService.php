<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityService
{
    /**
     * Get aggregated activity for a user (posts and comments).
     */
    public function getUserActivity($user, $perPage = 10)
    {
        $posts = Post::where('user_id', $user->id)
            ->select('id', 'user_id', 'content', 'created_at')
            ->selectRaw('"post" as type')
            ->get();

        $comments = Comment::where('user_id', $user->id)
            ->where('commentable_type', Post::class) // Only post comments for now for simplicity, or all? Plan said posts and comments on posts.
            ->with('commentable:id,content') // Load the post context
            ->select('id', 'user_id', 'content', 'commentable_id', 'commentable_type', 'created_at')
            ->selectRaw('"comment" as type')
            ->get();

        // Merge and sort
        $activities = $posts->concat($comments)->sortByDesc('created_at');

        // Paginate manually since we merged collections
        $page = LengthAwarePaginator::resolveCurrentPage();
        $results = $activities->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $activities->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }
}
