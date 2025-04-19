<?php

// app/Http/Controllers/FriendRequestController.php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class FriendRequestController extends Controller
{
    /**
     * Validate the friend request action.
     *
     * @param string $action
     * @return bool
     */
    private function validateFriendRequestAction(string $action)
    {
        return in_array($action, ['accept', 'reject']);
    }

    /**
     * Send a friend request.
     *
     * This method handles sending a friend request from the authenticated user (sender) to the specified user (receiver).
     * It ensures that a user cannot send a friend request to themselves, cannot send multiple requests to the same person, 
     * and cannot send a request to someone they are already friends with.
     *
     * @param User $receiver The user who will receive the friend request.
     * @return \Illuminate\Http\JsonResponse The response containing the result of the request.
     */
    public function send(User $receiver)
    {
        // Get the authenticated user (the sender of the friend request)
        /** @var User $sender */
        $sender = Auth::user();

        /** 
         * Check if the sender is the same as the receiver (cannot send a request to yourself).
         * If true, return an error response with a 400 status.
         */
        if ($sender->id === $receiver->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot send a friend request to yourself.'
            ], 400);
        }

        /** 
         * Check if the sender is already friends with the receiver.
         * If true, return an error response with a 400 status.
         */
        if ($sender->isFriendWith($receiver)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are already friends with this user.'
            ], 400);
        }

        /** 
         * Check if a pending friend request already exists from the sender to the receiver.
         * If true, return an error response with a 400 status.
         */
        if ($receiver->isFriendRequestReceivedBy($sender)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already sent a friend request to this user.'
            ], 400);
        }

        /** 
         * Create a new friend request in the database.
         * The sender’s ID and the receiver’s ID are saved in the 'friend_requests' table.
         */
        FriendRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        /** 
         * Return a success response indicating the friend request was successfully sent.
         */
        return response()->json([
            'status' => 'success',
            'message' => 'Friend request sent!'
        ]);
    }

    // Cancel a sent friend request
    /**
     * Cancel a sent friend request.
     *
     * @param User $receiver The user who received the friend request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(User $receiver)
    {
        // Get the authenticated user (the sender of the friend request)
        /** @var User $sender */
        $sender = Auth::user();
        $sender->sendFriendRequestTo($receiver)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Friend request cancelled successfully.'
        ]);
    }

    /**
     * Respond to a friend request by either accepting or rejecting it.
     *
     * This method allows the authenticated user (receiver) to either accept or reject a pending friend request 
     * sent by another user (sender). It ensures that the action is valid and prevents rejecting an already accepted request.
     *
     * @param User $sender The user who sent the friend request.
     * @param string $action The action to perform ('accept' or 'reject').
     * @return \Illuminate\Http\JsonResponse The response containing the result of the action.
     */
    public function respond(User $sender, string $action)
    {

        /** 
         * Validate the action (must be 'accept' or 'reject').
         * If the action is invalid, return an error response with a 400 status.
         */
        if (!$this->validateFriendRequestAction($action)) {
            return response()->json([
                'status' => 'error',
                'message' => "Invalid action. Allowed actions are 'accept' or 'reject'."
            ], 400);
        }
        // Get the authenticated user (the sender of the friend request)
        /** @var User $receiver */
        $receiver = Auth::user();
        /** 
         * If the friend request has already been accepted, prevent duplicate acceptance or rejection.
         * If the action is 'reject', return an error indicating that the request cannot be rejected.
         * If the action is 'accept', return an error indicating that the users are already friends.
         */
        $friendRequest = $receiver->receiveFriendRequestBy($sender);
        if ($sender->isFriendWith($receiver)) {
            if ($action === 'reject') {
                return response()->json([
                    'status' => 'error',
                    'message' => "You can't reject a friend request that you already accepted."
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'You are already friend with this user.'
            ], 400);
        }

        /** 
         * If the action is 'reject', delete the friend request from the database.
         * Return a success response indicating the request was rejected and deleted.
         */
        if ($action === 'reject') {
            $receiver->receiveFriendRequestBy($sender)->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Friend request rejected and deleted.'
            ]);
        }

        /** 
         * If the action is 'accept', update the request status to 'accepted' in the database.
         * Return a success response indicating the request was accepted.
         */
        $friendRequest->update(['status' => 'accepted']);
        return response()->json([
            'status' => 'success',
            'message' => 'Friend request accepted.'
        ]);
    }
    /**
     * Get all the friend requests received by the authenticated user with pagination.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function received(Request $request)
    {
        // Get the authenticated user (the receiver of the friend request)
        /** @var User $receiver */
        $receiver = Auth::user();
        $page = $request->query('page', 1);

        // Retrieve all the friend requests received by the user along with the sender's information
        // Paginate the result, 10 requests per page (you can adjust the number as needed)
        $receivedRequests = $receiver->receivedFriendRequests()->with('sender.profile')->latest()->paginate(5);

        // Return the response with the received friend requests
        return response()->json([
            'friendRequests' => $receivedRequests->items(),
            'nextPage' => $receivedRequests->hasMorePages() ? $page + 1 : null,
        ]);
    }
    public function received2(){
        // Get the authenticated user (the receiver of the friend request)
        /** @var User $receiver */
        $receiver = Auth::user();

        // // Retrieve all the friend requests received by the user along with the sender's information
        // // Paginate the result, 10 requests per page (you can adjust the number as needed)
        // $receivedRequests = $receiver->friendRequestsReceived()->with('sender.profile')->latest()->paginate(5);

        // Return the response with the received friend requests
        return $receiver->receivedFriendRequests()->latest()->get() ;
    }
    public function sent2(){
        // Get the authenticated user (the receiver of the friend request)
        /** @var User $sender */
        $sender = Auth::user();

        // // Retrieve all the friend requests received by the user along with the sender's information
        // // Paginate the result, 10 requests per page (you can adjust the number as needed)
        // $receivedRequests = $receiver->friendRequestsReceived()->with('sender.profile')->latest()->paginate(5);

        // Return the response with the received friend requests
        return $sender->sentFriendRequests()->latest()->get() ;
    }

    /**
     * Get all the friend requests sent by the authenticated user with pagination.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sent()
    {
        // Get the authenticated user (the sender of the friend request)
        /** @var User $sender */
        $sender = Auth::user();

        // Retrieve all the friend requests sent by the user along with the receiver's information
        // Paginate the result, 10 requests per page (you can adjust the number as needed)
        $sentRequests = $sender->sentFriendRequests()->with('receiver')->paginate(10);

        // Return the response with the sent friend requests
        return response()->json([
            'status' => 'success',
            'friend_requests' => $sentRequests
        ]);
    }



}
