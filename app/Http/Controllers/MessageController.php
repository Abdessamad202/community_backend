<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Events\MessageSent;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\TypingEvent;

class MessageController extends Controller
{
    /**
     * Send a message to a specific conversation.
     *
     * This function allows the authenticated user to send a message to a conversation.
     * It checks if the user is part of the conversation and sends a message to the other participant.
     *
     * @param Request $request
     * @param Conversation $conversation
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        /**
         * @var User $sender
         */
        $sender = Auth::user();

        // Gate::authorize("partOf", $conversation);
        /**
         * @var Message $message
         */
        $message = $conversation->messages()->make([
            "content" => $request->content,
        ]);
        $message->sender()->associate($sender);
        $message->save();
        // dump($message);
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully.',
            'data' => $message,
        ]);
    }

    public function typing(Request $request)
    {
        broadcast(new TypingEvent($request->conversationId, Auth::id()))->toOthers();
        return response()->json(['status' => 'typing broadcasted']);
    }
    public function readMessages(Conversation  $conversation)
    {
        $conversation->readed();
        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Update an existing message.
     *
     * This function allows the sender of a message to update the message content.
     * The user must be the sender of the message to perform this action.
     *
     * @param Request $request
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Message $message)
    {
        // Ensure the user is the sender of the message
        Gate::authorize('isSenderOf', $message);

        // Validate the new message content
        $request->validate([
            'message' => 'required|string',
        ]);

        // Update the message
        $message->update([
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => 'Message updated successfully', 'data' => $message]);
    }

    /**
     * Delete a message.
     *
     * This function allows the sender of a message to delete it.
     * The user must be the sender of the message to perform this action.
     *
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Message $message)
    {
        // Ensure the user is authorized to delete the message
        Gate::authorize('isSenderOf', $message);

        // Delete the message
        $message->delete();

        return response()->json(['message' => 'Message deleted successfully.']);
    }
    // public function sendTest(Request $request)
    // {
    //     return response()->json(['message' => $request->message]);
    // }
}
