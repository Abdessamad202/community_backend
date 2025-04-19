<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get authenticated user's data from the 'users' and 'profiles' tables.
     */
    public function profile(User $user)
    {
        $authUser = auth()->user(); // Get the authenticated user

        $user->load([
            'profile',
            'friends.profile',
            'posts' => function ($query) {
                $query->with(['comments.user.profile', 'user.profile'])
                    ->withCount(['likes', 'comments'])
                    ->latest(); // Get posts with latest comments
            },
        ])->loadCount(['friends', 'posts']); // Add counts for friends and posts

        // Add friendship status directly to the user object
        $user->friendship_status = $authUser->determineFriendshipStatus($user);

        return response()->json([
            'user' => $user,
        ]);
    }



    public function showAuthenticatedUserData()
    {
        // Get authenticated user and their profile
        $user = Auth::user()->load('profile');

        // Prepare the data to be returned
        $userData = [
            'id' => $user->id,
            'email' => $user->email,
            'is_verified' => $user->is_verified,
            'registration_status' => $user->registration_status,
            'profile' => $user->profile ? [
                'name' => $user->profile->name,
                'date_of_birth' => $user->profile->date_of_birth,
                'gender' => $user->profile->gender,
                'picture' => $user->profile->picture,
                'description' => $user->profile->description,
            ] : null,
        ];

        return response()->json($userData);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
                'errors' => [
                    'current_password' => 'Current password is incorrect.',
                ]
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['message' => 'Password changed successfully.']);
    }
}
