<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Determine whether the user can send a message in the given conversation.
     *
     * This method checks whether the user is involved in the conversation 
     * (i.e., is either user 1 or user 2 in the conversation).
     *
     * @param  \App\Models\User  $user        The user trying to send a message.
     * @param  \App\Models\Conversation  $conversation  The conversation the user wants to send a message in.
     * @return bool  Returns true if the user is involved in the conversation, otherwise false.
     */
    public function partOf(User $user, Conversation $conversation)
    {
        return $conversation->involves($user);
    }
}
