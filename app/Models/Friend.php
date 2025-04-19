<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    /** @use HasFactory<\Database\Factories\FriendFactory> */
    use HasFactory;
    protected $fillable = [
        "user_id",
        "friend_id"
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::deleting(function ($friend) {
            // Delete associated friend requests
            FriendRequest::where('sender_id', $friend->user_id)
                ->orWhere('receiver_id', $friend->user_id)
                ->delete();
            // Delete associated conversations
            Conversation::where('user_id_1', $friend->user_id)
                ->orWhere('user_id_2', $friend->user_id)
                ->delete();
        });
    }
}
