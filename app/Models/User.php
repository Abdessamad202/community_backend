<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Authorizable;
    // Friendship status constants
    private const STATUS_SELF = 'self';
    private const STATUS_FRIEND = 'friend';
    private const STATUS_SENT = 'sent';
    private const STATUS_RECEIVED = 'received';
    private const STATUS_NONE = 'none';
    protected $fillable = [
        'email',
        'password',
        'is_verified',
        'registration_status',
    ];

    /**
     * Get the user's profile (one-to-one relationship).
     * 
     * The user has a unique profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's verification details (one-to-one relationship).
     * 
     * The user has one verification record (e.g., email verification).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    /**
     * Get the posts created by the user (one-to-many relationship).
     * 
     * The user can create multiple posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the friend requests sent by the user (one-to-many relationship).
     * 
     * The user can send friend requests to others.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id')->where('status', 'pending');
    }
    public function sendFriendRequestTo(User $receiver)
    {
        return $this->sentFriendRequests()->where('receiver_id', $receiver->id)->first();
    }

    /**
     *
     * @return FriendRequest
     */
    public function receiveFriendRequestBy(User $sender)
    {
        return $this->receivedFriendRequests()->where('sender_id', $sender->id)->first();
    }
    /**
     * Get all the friend requests received by the user.
     * 
     * The user can receive multiple friend requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id')->where('status', 'pending');
    }
    /**
     * Check if the user has received a friend request from a specific sender.
     * 
     * This method checks if the current user has received a friend request from the 
     * given sender user. It looks up the friend's requests the current user has received 
     * and verifies if a request from the specified sender exists.
     *
     * @param User $sender The user who may have sent the friend request.
     * @return bool Returns true if a friend request exists from the sender, false otherwise.
     */
    public function isFriendRequestReceivedBy(User $sender)
    {
        // Check if a friend request exists from the given sender
        return $this->receivedFriendRequests()->where('sender_id', $sender->id)->exists();
    }
    public function isFriendRequestSentTo(User $receiver)
    {
        // Check if a friend request exists from the given sender
        return $this->sentFriendRequests()->where('receiver_id', $receiver->id)->exists();
    }


    /**
     * Get the user's friends (many-to-many relationship).
     * 
     * The user can have multiple friends once the friendship is accepted.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')->withTimestamps();
    }

    /**
     * Check if the user is friends with another user.
     * 
     * This method checks if a specific user is a friend of the current user.
     *
     * @param User $user The user to check friendship status with.
     * @return bool True if friends, otherwise false.
     */
    public function isFriendWith(User $user)
    {
        return $this->friends()->where('friend_id', $user->id)->exists();
    }
    /**
     * Determine the friendship status between the authenticated user and the otherUser.
     */
    public function determineFriendshipStatus(User $otherUser): string
    {
        if ($otherUser->id == $this->id) {
            return self::STATUS_SELF;
        }

        if ($this->isFriendWith($otherUser)) {
            return self::STATUS_FRIEND;
        }

        if ($this->isFriendRequestSentTo($otherUser)) {
            return self::STATUS_SENT;
        }

        if ($this->isFriendRequestReceivedBy($otherUser)) {
            return self::STATUS_RECEIVED;
        }

        return self::STATUS_NONE;
    }
    public function savedPosts()
    {
        return $this->belongsToMany(Post::class, 'saved_posts')->withTimestamps();
    }
    // Check if a post is already saved
    public function hasSaved(Post $post): bool
    {
        return $this->savedPosts()->where('post_id', $post->id)->exists();
    }

    // Toggle save/unsave based on current status
    public function toggleSave(Post $post): string
    {
        $this->savedPosts()->toggle($post->id);
        return $this->hasSaved($post) ? 'saved' : 'unsaved';
    }

    /**
     * Define a many-to-many relationship between the User model and the Conversation model.
     *
     * This method establishes a relationship where a user can participate in multiple conversations,
     * and a conversation can have multiple users. The relationship is facilitated through the
     * 'conversation_user' pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withTimestamps();
    }

    /**
     * Get the messages sent by the user.
     *
     * This defines a one-to-many relationship between the User model
     * and the Message model, where the 'sender_id' in the Message model
     * corresponds to the primary key of the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Automatically create a profile when the registration status is set to 'completed'.
     * 
     * If the user completes registration and doesn't have a profile, a profile will be created.
     */
    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->registration_status == 'completed' && !$user->profile) {
                $user->profile()->create(request()->all());
            }
        });
    }
    protected $hidden = [
        'password',
    ];
}
