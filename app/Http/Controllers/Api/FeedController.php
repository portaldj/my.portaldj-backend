<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    protected $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function index()
    {
        return $this->feedService->getGlobalFeed();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'max:5120'], // 5MB
            'location_tag_type' => ['nullable', 'string', 'in:city,club,user'],
            'location_tag_id' => ['nullable', 'integer'],
        ]);

        $post = $this->feedService->createPost($request->user(), $validated);

        return response()->json($post, 201);
    }

    public function comment(Request $request, $postId)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        $comment = $this->feedService->addComment($request->user(), $postId, $validated['content']);

        return response()->json($comment, 201);
    }
}
