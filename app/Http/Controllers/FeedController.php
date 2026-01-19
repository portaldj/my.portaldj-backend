<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeedService;
use Illuminate\Http\RedirectResponse;

class FeedController extends Controller
{
    protected $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:120',
            'image' => 'nullable|image|max:5120', // 5MB
            'location_tag_type' => 'nullable|string|in:club,user,city',
            'location_tag_id' => 'nullable|integer',
        ]);

        $this->feedService->createPost($request->user(), $validated);

        return redirect()->back()->with('success', 'Post created successfully!');
    }

    public function like($post): RedirectResponse
    {
        $this->feedService->toggleLike(auth()->user(), $post);
        return redirect()->back(); // Inertia will reload props
    }

    public function comment(Request $request, $post): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $this->feedService->addComment(auth()->user(), $post, $validated['content']);

        return redirect()->back();
    }

    public function destroy($postId): RedirectResponse
    {
        $this->feedService->deletePost(auth()->user(), $postId);
        return redirect()->back()->with('success', 'Post deleted successfully.');
    }

    public function destroyComment($commentId): RedirectResponse
    {
        $this->feedService->deleteComment(auth()->user(), $commentId);
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
