<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SavedPostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $savedPosts = $user->savedPosts()
        ->with('user.profile')
        ->withCount(['likes', 'comments'])
        ->latest('saved_posts.created_at') // Order by the pivot table's created_at column in descending order
        ->get();
    
        return response()->json([
            'posts' => $savedPosts
        ]);
    }
}
