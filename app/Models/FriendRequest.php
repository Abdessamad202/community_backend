<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

    // The attributes that are mass assignable
    protected $fillable = ['sender_id', 'receiver_id', 'status'];

    /**
     * Relationship to the sender of the friend request.
     * 
     * A friend request is initiated by the sender.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship to the receiver of the friend request.
     * 
     * A friend request is sent to the receiver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Check if the status of the friend request was changed to "accepted".
     *
     * This method checks if the status of the request is "accepted" and
     * if the status attribute has been modified during the update.
     *
     * @return bool Returns true if the status was changed to "accepted", false otherwise.
     */
    private function isStatusChangedToAccepted(): bool
    {
        return $this->isDirty('status') && $this->status === 'accepted';
    }
    /**
     * Accepts the friend request by updating its status to 'accepted'.
     *
     * @return bool Indicates whether the update operation was successful.
     */
    public function acceptFriendRequest(){
        return $this->update(['status' => 'accepted']);
    }
    /**
     * Boot method for handling actions when a friend request is updated.
     * 
     * This method ensures that mutual friendships and a conversation are created
     * when a friend request is accepted.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::updated(function (FriendRequest $friendRequest) {
            // If the status was not changed to "accepted", exit early
            if (!$friendRequest->isStatusChangedToAccepted()) {
                return;
            }

            // Create mutual friend records
            Friend::firstOrCreate([
                'user_id' => $friendRequest->sender_id,
                'friend_id' => $friendRequest->receiver_id,
            ]);

            Friend::firstOrCreate([
                'user_id' => $friendRequest->receiver_id,
                'friend_id' => $friendRequest->sender_id,
            ]);

            // Check if a conversation already exists between the two
            $conversation = Conversation::whereHas('participants', function ($q) use ($friendRequest) {
                $q->whereIn('user_id', [$friendRequest->sender_id, $friendRequest->receiver_id]);
            }, '=', 2)->first();

            // If no conversation exists, create one and attach both users
            if (!$conversation) {
                $conversation = Conversation::create();
                $conversation->participants()->attach([
                    $friendRequest->sender_id,
                    $friendRequest->receiver_id
                ]);
            }
            // Delete the friend request after acceptance
            $friendRequest->delete();
        });
    }


    /**
     * Check if the friend request is already accepted.
     *
     * This method checks if the status of the friend request is "accepted".
     *
     * @return bool Returns true if the request has been accepted, false otherwise.
     */
    public function isAlreadyAccepted(): bool
    {
        return $this->status === 'accepted';
    }
}
