<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function index()
    {

        /**
         * Retrieve the currently authenticated user.
         *
         * @var \App\Models\User|null $user The authenticated user instance or null if no user is authenticated.
         */
        $user = Auth::user(); // Get authenticated user
        // Retrieve all conversations where the user is part of (either user_id_1 or user_id_2)
        $conversations = $user->conversations()->with(['participant.profile', 'messages'])->latest("last_message_at")->paginate(10); // Paginate the results
        return  response()->json([
            'conversations' => $conversations->items(),
            'nextPage' => $conversations->hasMorePages() ? $conversations->currentPage() + 1 : null,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified conversation and its messages.
     *
     * This method retrieves the specified conversation and the messages associated with it.
     * It sorts the messages by their creation date, showing the latest messages first.
     *
     * @param \App\Models\Conversation $conversation The conversation to display
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the conversation and its messages
     */
    public function show(Conversation $conversation)
    {
        // Fetch messages in the conversation, ordered by the latest created_at timestamp
        // $messages = $conversation->messages()->latest('created_at')->paginate(10);
        $messages = $conversation->messages()->latest('created_at')->get();

        // Return the conversation and messages as a JSON response
        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConversationRequest $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //

    }
}
