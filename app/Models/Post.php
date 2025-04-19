<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    protected $fillable = ['user_id', 'content', 'image'];

    /**
     * Get the user who created the post.
     * A post belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the likes associated with the post.
     * A post can have many likes.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the comments associated with the post.
     * A post can have many comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'saved_posts')->withTimestamps();
    }
}
