<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChapterCommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Chapter $chapter)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $chapter->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Comment posted successfully.');
    }
}
