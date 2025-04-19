<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Like or Unlike a post.
     */
    public function toggle(Post $post)
    {
        $existingLike = Like::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // If the user has already liked the post, unlike it
            $existingLike->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Post unliked'
            ]);
        }

        // Otherwise, like the post
        Like::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post liked'
        ]);
    }
}
