<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['last_message_at'];

    /**
     * Define a many-to-many relationship between the Conversation model and the User model.
     *
     * This method retrieves the participants of a conversation by establishing
     * a relationship with the 'conversation_user' pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user');
    }



    public function participant()
    {
        return $this->participants()->where('user_id', '!=', Auth::id());
    }

    /**
     * Get the messages associated with the conversation.
     *
     * This method defines a one-to-many relationship between the Conversation
     * model and the Message model. It allows you to retrieve all messages
     * that belong to a specific conversation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->oldest('created_at');
    }
    /**
     * Check if a given user is a participant in this conversation.
     *
     * A conversation involves at least two users (currently), identified through a participants relationship.
     * This method checks whether the provided user is among them.
     *
     * @param  User  $participant  The user instance to check.
     * @return bool  Returns true if the user is part of the conversation, otherwise false.
     */
    public function involves(User $participant)
    {
        return $this->participants()->where('id', $participant->id)->exists();
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     * 
     * 'last_message_at' will be automatically cast to a DateTime instance.
     */
    protected $casts = [
        'last_message_at' => 'datetime',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     "pivot"
    // ];
}
