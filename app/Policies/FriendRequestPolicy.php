<?php

namespace App\Policies;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FriendRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function respond($sender,$receiver): bool
    {
        return $sender == $receiver;
    }
}
