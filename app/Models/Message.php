<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    protected $fillable = [
        'sender_id',
        'content',
        'reply_to',
        'is_read',
        'conversation_id',
    ];

    /**
     * The boot method is used to listen for specific events on the model.
     * Here, we are updating the `last_message_at` timestamp in the conversation 
     * when a new message is created.
     */
    protected static function booted()
    {
        static::created(function (Message $message) {
            // Update the last_message_at field in the conversation
            $message->conversation()->update(['last_message_at' => $message->created_at]);
            // $message->conversation->update(['last_message_at' => $message->created_at]);
        });
    }

    // Relationships
    /**
     * Get the conversation that this message belongs to.
     * 
     * This method defines a one-to-many relationship between the Message model
     * and the Conversation model. It allows you to retrieve the conversation
     * associated with a specific message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that sent the message.
     *
     * This defines an inverse one-to-many relationship between the Message model
     * and the User model, where the 'sender_id' foreign key in the messages table
     * references the id of the user who sent the message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }


    /**
     * Determine if the given user is the sender of the message.
     *
     * @param User $user The user to check against the sender of the message.
     * @return bool True if the user is the sender, false otherwise.
     */
    public function isSender(User $user)
    {
        return $this->sender_id == $user->id;
    }
    /**
     * Get the parent message that this message is replying to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function parentMessage()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }
    /**
     * Get the replies associated with this message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to');
    }
    
}
