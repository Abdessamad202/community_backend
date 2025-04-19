<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    /**
     * Get all posts with likes & comments count, with pagination.
     */
    public function index(Request $request)
    {
        $posts = Post::withCount(['likes', 'comments'])
            ->with(['user.profile', 'comments.user.profile'])
            ->latest()->paginate(10);

        return response()->json([
            'posts' => $posts->items(),
            'nextPage' => $posts->hasMorePages() ? $posts->currentPage() + 1 : null,
        ]);
    }

    /**
     * Get all posts of a specific user with likes & comments count.
     */
    public function show(User $user, Request $request)
    {
        $posts = $user->posts()
            ->withCount(['likes', 'comments'])
            ->with(['user.profile', 'comments.user.profile', 'likes.user.profile'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'posts' => $posts->items(),
            'nextPage' => $posts->hasMorePages() ? $posts->currentPage() + 1 : null,
        ]);
    }
    public function toggleSave(Post $post)
    {
        $user = Auth::user();
        $status = $user->toggleSave($post);
        return response()->json([
            'status' => "success",
            'message' => $status === 'saved'
                ? 'Post saved successfully.'
                : 'Post removed from saved list.',
        ]);
    }


    /**
     * Show a single post with related likes, comments, and user profile.
     */
    public function showSinglePost(Post $post)
    {
        $postData = $post->load([
            'likes.user.profile',
            'comments.user.profile',
            'user.profile'
        ])->loadCount(["comments", "likes"]);

        return response()->json($postData);
    }

    /**
     * Create a new post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('posts', 'public');
            $imagePath = url(Storage::url($filePath));
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'image' => $imagePath
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => $post
        ]);
    }

    /**
     * Update a post.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $post->image;

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('posts', 'public');
            $imagePath = url(Storage::url($filePath));
        }

        $post->update([
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        if ($post->image) {
            $imagePath = str_replace(url('/storage'), 'public', $post->image);
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully.'
        ], 200);
    }

    /**
     * Retrieve likers of a post with their friendship status relative to the authenticated user.
     */
    public function likersWithStatus(Post $post): JsonResponse
    {
        /**
         * @var User $authUser
         */
        $authUser = Auth::user();

        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $authUser->load(['friends', 'sentFriendRequests', 'receivedFriendRequests']);

        $likers = $post->likes()
            ->with('user.profile')
            ->get()
            ->map(function ($like) use ($authUser) {
                return $this->formatLikerData($like->user, $authUser);
            });

        return response()->json($likers);
    }

    /**
     * Format a liker's data with their friendship status.
     */
    private function formatLikerData(User $liker, User $authUser): array
    {
        $status = $authUser->determineFriendshipStatus($liker);

        return [
            'id' => $liker->id,
            'name' => $liker->profile->name ?? 'Unknown',
            'picture' => $liker->profile->picture ?? null,
            'friendship_status' => $status,
        ];
    }
}
