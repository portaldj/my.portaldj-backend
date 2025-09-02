<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use App\Traits\ImageUploadControllerTrait;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PostController extends Controller
{
    use ImageUploadControllerTrait;
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $posts = Post::with('user', 'image')->latest()->paginate(15);
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request): PostResource
    {
        $post = $this->postService->createPost($request->user(), $request->validated());
        return new PostResource($post);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post->load(['user', 'image', 'comments.user', 'schedule.club']));
    }

    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        Gate::authorize('update', $post);

        $updatedPost = $this->postService->updatePost($post, $request->validated());

        return new PostResource($updatedPost);
    }

    public function destroy(Post $post): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('delete', $post);
        $post->delete();
        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
