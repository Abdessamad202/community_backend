<?php
// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'user_id','name','gender','date_of_birth','picture','description'
    ];
    /**
     * Get the user who created the profile.
     * A profile belongs to a user.
     */
    public function user (){
        return $this->belongsTo(User::class);
    }
}
