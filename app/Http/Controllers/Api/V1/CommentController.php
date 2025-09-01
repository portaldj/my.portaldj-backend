<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, Post $post): CommentResource
    {
        $comment = $post->comments()->create([
            'content' => $request->validated('content'),
            'user_id' => auth()->id(),
        ]);

        return new CommentResource($comment->load('user'));
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        Gate::authorize('update', $comment);

        $comment->update($request->validated());

        return new CommentResource($comment->load('user'));
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}
