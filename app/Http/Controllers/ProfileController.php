<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Example registration logic in the controller

    public function completeProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:M,F',
            'picture' => 'nullable',
            // 'picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'description' => 'nullable|string',
        ]);

        $user = $request->user();

        // Check if registration is already completed
        if ($user->registration_status === 'completed') {
            return response()->json([
                'message' => 'Your profile is already completed.',
                'regestration_status' => $user->regestration_status
            ], 409); // HTTP 409 Conflict
        }

        // Update the user profile
        $user->registration_status = 'completed';
        $user->save();

        return response()->json([
            'message' => 'Profile completed successfully.',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'registration_status' => $user->registration_status,
                'token' =>$user->currentAccessToken()->plainTextToken,
            ],
        ], 200);
    }

    public function test()
    {
        return response()->json([
            'message' => 'welcome to the home page',
        ]);
    }
    public function search(Request $request)
    {
        // Validate the search query
        $request->validate([
            'query' => 'required|string|min:1|max:255',
        ]);

        $query = $request->input('query');

        try {
            $users = User::whereHas('profile', function ($q) use ($query) {
                $q->where('name', 'like', $query . '%');
            })
                ->with(['profile:id,user_id,name,picture']) // Eager load only needed fields
                ->select('id') // Just get user id (other fields are in profile)
                ->limit(10)
                ->get()
                ->map(function (User $user) {
                    return [
                        'id' => $user->id,
                        'profile' => [
                            'name' => $user->profile->name,
                            'picture' => $user->profile->picture,
                            'title' => $user->profile->title,
                        ],
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while searching for users',
            ], 500);
        }
    }
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        // Validate the incoming request data (returns an array)
        $data = $request->validated();

        // Initialize the image path to the user's current picture, or default if null
        $imagePath = $user->profile->picture ?? url('imgs/profiles/user.jpg');

        // Check if 'picture' is explicitly sent in the request
        if ($request->has('picture')) {
            // If picture is sent as "null" (string from FormData), use the default image
            if ($request->input('picture') === 'null') {
                $imagePath = url('imgs/profiles/user.jpg');
            }
            // If a new image file is uploaded, store and use its path
            elseif ($request->hasFile('picture')) {
                $filePath = $request->file('picture')->store('profiles', 'public');
                $imagePath = url(Storage::url($filePath));
            }
        }

        // Prepare the data to update, including the image path
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'gender' => $data['gender'] ?? $user->gender,
            'description' => $data['description'] ?? $user->description,
            'picture' => $imagePath,
        ];

        // Update the user's profile (assuming a profile relationship exists)
        $user->profile->update($updateData);
        // If you meant to update the User model directly, use:
        // $user->update($updateData);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

}
