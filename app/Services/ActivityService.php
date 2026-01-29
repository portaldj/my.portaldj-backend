<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\BookingPromotion;
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

        $promotions = BookingPromotion::whereHas('event', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->with('event:id,title')
            ->get()
            ->map(function ($promo) {
                return (object) [
                    'id' => $promo->id,
                    'user_id' => $promo->event->user_id, // Though indirect
                    'content' => $promo->press_release,
                    'created_at' => $promo->created_at,
                    'type' => 'booking_promotion',
                    'status' => $promo->status,
                    'rejection_reason' => $promo->rejection_reason,
                    'blog_url' => $promo->blog_url,
                    'event_title' => $promo->event->title,
                ];
            });

        // Merge and sort
        $activities = $posts->concat($comments)->concat($promotions)->sortByDesc('created_at');

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
