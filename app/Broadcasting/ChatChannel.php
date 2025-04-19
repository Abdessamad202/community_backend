<?php

namespace App\Broadcasting;

use App\Models\Conversation;
use App\Models\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user,Conversation $conversation): array|bool
    {
        //
        // return true;
        return $conversation->involves($user);
    }
}
