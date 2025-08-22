<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Check if the verification token is expired (default: 24 hours).
     */
    public function isExpired($hours = 24)
    {
        return $this->created_at->addHours($hours)->isPast();
    }

    /**
     * Check if the verification token is still valid.
     */
    public function isValid($hours = 24)
    {
        return !$this->isExpired($hours);
    }

    /**
     * Generate a new verification token.
     */
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create a new email verification record.
     */
    public static function createForEmail($email)
    {
        return static::create([
            'email' => $email,
            'token' => static::generateToken(),
            'created_at' => now(),
        ]);
    }
}
