<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // The attributes that are mass assignable
    protected $fillable = ['user_id', 'post_id', 'content'];

    /**
     * Get the user that owns the comment.
     * This defines the relationship where each comment belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that the comment belongs to.
     * This defines the relationship where each comment belongs to a post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
