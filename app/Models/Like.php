<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    // The attributes that are mass assignable
    protected $fillable = ['user_id', 'post_id'];

    /**
     * Relationship to the user who liked the post.
     * A like is made by a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the post that was liked.
     * A like is associated with a specific post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
