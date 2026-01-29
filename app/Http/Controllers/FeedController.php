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

    public function show($postId)
    {
        $post = \App\Models\Post::with(['user.profile', 'comments.user.profile', 'likes', 'taggedClubs', 'taggedCities', 'taggedDjs', 'locationTag'])
            ->withCount(['likes', 'comments'])
            ->withExists([
                'likes as is_liked' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ])
            ->findOrFail($postId);

        return \Inertia\Inertia::render('Feed/Show', [
            'post' => $post
        ]);
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
            'tags' => 'nullable|array',
            'tags.*.id' => 'required|integer',
            'tags.*.type' => 'required|string|in:club,city,user',
        ]);

        $this->feedService->createPost($request->user(), $validated);

        return redirect()->back()->with('success', __('Post created successfully!'));
    }

    public function like($post)
    {
        $liked = $this->feedService->toggleLike(auth()->user(), $post);
        return response()->json(['liked' => $liked]);
    }

    public function comment(Request $request, $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $this->feedService->addComment(auth()->user(), $post, $validated['content']);

        // Eager load user profile for frontend display
        $comment->load('user.profile');

        return response()->json(['comment' => $comment, 'message' => __('Comment added successfully.')]);
    }

    public function destroy($postId)
    {
        $this->feedService->deletePost(auth()->user(), $postId);
        return response()->json(['message' => __('Post deleted successfully.')]);
    }

    public function destroyComment($commentId)
    {
        $this->feedService->deleteComment(auth()->user(), $commentId);
        return response()->json(['message' => __('Comment deleted successfully.')]);
    }

    public function getComments(Request $request, $postId)
    {
        $post = \App\Models\Post::findOrFail($postId);

        $comments = $post->comments()
            ->with('user.profile')
            ->latest()
            ->paginate(5); // Default page query param handles offset

        return response()->json($comments);
    }
}
