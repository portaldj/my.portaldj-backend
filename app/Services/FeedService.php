<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FeedService
{
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
            $postData['image_path'] = $data['image']->store('posts', 'public');
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
    public function getGlobalFeed(int $perPage = 20)
    {
        return Post::with(['user.profile', 'comments.user.profile', 'taggedClubs', 'taggedCities', 'taggedDjs'])
            ->withCount(['likes', 'comments'])
            ->withExists([
                'likes as is_liked' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ])
            ->latest()
            ->paginate($perPage);
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
}
