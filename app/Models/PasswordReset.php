<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table = "password_resets"; // Explicitly defining table name

    protected $fillable = ['email', 'code', 'expires_at'];

    public $timestamps = false;

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the reset code is expired.
     */
    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}
