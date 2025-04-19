<?php

// app/Models/Verification.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'code', 'expires_at'
    ];

    /**
     * Get the user associated with the verification.
     * A verification record belongs to a single user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the verification code has expired.
     * The expiration is determined by the 'expires_at' field.
     */
    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}
