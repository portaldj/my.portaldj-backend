<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\ImageOptimizationService;

class FeedService
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Create a new post
     */
    public function createPost(User $user, array $data)
    {
        $postData = [
            'content' => $data['content'],
            // Legacy columns can be null now
        ];

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Create optimized version (max width 1080, auto height)
            $variants = ['optimized' => [1080, null]];
            $images = $this->imageService->handle($data['image'], 'posts', $variants);
            $postData['image_path'] = $images['original'];
        }

        $post = $user->posts()->create($postData);

        // Handle Tags
        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $tag) {
                // Ensure tag has id and type
                if (!isset($tag['id']) || !isset($tag['type']))
                    continue;

                if ($tag['type'] === 'club') {
                    $post->taggedClubs()->attach($tag['id']);
                } elseif ($tag['type'] === 'city') {
                    $post->taggedCities()->attach($tag['id']);
                } elseif ($tag['type'] === 'user') {
                    $post->taggedDjs()->attach($tag['id']);
                }
            }
        }

        return $post;
    }

    /**
     * Add a comment to a post
     */
    public function addComment(User $user, int $postId, string $content)
    {
        $post = Post::findOrFail($postId);

        return $post->comments()->create([
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    /**
     * Get global feed
     */
    public function getGlobalFeed(int $perPage = 10)
    {
        $feed = Post::with(['user.profile', 'taggedClubs', 'taggedCities', 'taggedDjs.profile'])
            ->withCount(['likes', 'comments'])
            ->withExists([
                'likes as is_liked' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ])
            ->latest()
            ->paginate($perPage);

        // Manually load top 5 comments for each post to avoid N+1 full load or complex subqueries
        $feed->getCollection()->each(function ($post) {
            $post->setRelation('comments', $post->comments()
                ->with('user.profile')
                ->latest()
                ->limit(5)
                ->get());
        });

        return $feed;

    }

    /**
     * Toggle like on a post
     */
    public function toggleLike(User $user, int $postId)
    {
        $post = Post::findOrFail($postId);

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return false; // unliked
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            return true; // liked
        }
    }

    /**
     * Delete a post
     */
    public function deletePost(User $user, int $postId)
    {
        $post = Post::findOrFail($postId);

        if ($user->id !== $post->user_id && !$user->hasRole('Admin')) {
            abort(403, 'Unauthorized');
        }

        if ($post->image_path) {
            $this->imageService->delete($post->image_path, ['optimized']);
        }

        $post->delete();
    }

    /**
     * Delete a comment
     */
    public function deleteComment(User $user, int $commentId)
    {
        $comment = \App\Models\Comment::findOrFail($commentId);

        // Allow comment owner OR post owner OR Admin to delete
        // If I'm the post owner, I should be able to delete comments on my post? Usually yes.
        // User asked for "Admin users ... have option to delete posts or comments".
        // I'll stick to Admin or Comment Owner for now, maybe Post Owner too.

        $isPostOwner = $comment->post->user_id === $user->id;

        if ($user->id !== $comment->user_id && !$user->hasRole('Admin') && !$isPostOwner) {
            abort(403, 'Unauthorized');
        }

        $comment->delete();
    }
}
