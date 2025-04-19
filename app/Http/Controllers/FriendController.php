<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FriendController extends Controller
{
    /**
     * List all friends of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get the authenticated user
        /** @var User $user */
        $user = Auth::user();

        // Retrieve all friends (many-to-many relationship)
        $friends = $user->friends()->with('profile')->paginate(10); // Adjust pagination as necessary

        return response()->json([
            'friends' => $friends->items(),
            'nextPage' => $friends->nextPageUrl(),
        ]);
    }
    public function index2(){
        // Get the authenticated user (the receiver of the friend request)
        /** @var User $receiver */
        $receiver = Auth::user();

        // // Retrieve all the friend requests received by the user along with the sender's information
        // // Paginate the result, 10 requests per page (you can adjust the number as needed)
        // $receivedRequests = $receiver->friendRequestsReceived()->with('sender.profile')->latest()->paginate(5);

        // Return the response with the received friend requests
        return $receiver->friends()->latest()->get() ;
    }

    /**
     * Remove a friend from the authenticated user's friend list.
     *
     * @param User $friend The user who will receive the friend request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $friend)
    {
        // Get the authenticated user
        /** @var User $user */
        $user = Auth::user();

        // Check if the user is friends with the provided user
        if (!$user->isFriendWith( $friend)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This user is not in your friend list.'
            ], 400);
        }

        // Remove the friendship (delete the relationship in the pivot table)
        $user->friends()->detach($friend->id);
        $friend->friends()->detach($user->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Friend removed successfully.'
        ]);
    }
}
