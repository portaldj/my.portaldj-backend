<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class PostService
{
    /**
     * @param User $user
     * @param array $data
     * @param UploadedFile|null $image
     * @return Post
     */
    public function createPost(User $user, array $data, ?UploadedFile $image): Post
    {
        $post = $user->posts()->create($data);

        if ($image) {
            $path = $image->store('posts', 'public');
            $post->image()->create(['path' => $path]);
        }

        return $post;
    }
}
