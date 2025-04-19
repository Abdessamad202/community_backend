<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Comment added', 'data' => $comment]);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request,Post $post,Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request: This comment does not belong to the specified post.'
            ], 400);
        }
        Gate::authorize('update', $comment);

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Comment updated', 'data' => $comment]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Post $post,Comment $comment)
    {
        // Ensure the comment belongs to the post
        if ($comment->post_id !== $post->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request: This comment does not belong to the specified post.'
            ], 400);
        }

        // Check authorization
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }

}
