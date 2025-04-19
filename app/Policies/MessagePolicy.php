<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Check if the authenticated user is the sender of the message.
     *
     * @param  User    $sender  The user attempting the action.
     * @param  Message $message The message being acted upon.
     * @return bool
     */
    public function isSenderOf(User $sender, Message $message)
    {
        return $message->isSender($sender);
    }
}
