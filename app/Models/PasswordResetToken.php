<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_reset_tokens';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'email';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

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
     * Check if the reset token is expired (default: 24 hours).
     */
    public function isExpired($hours = 24)
    {
        return $this->created_at->addHours($hours)->isPast();
    }

    /**
     * Check if the reset token is still valid.
     */
    public function isValid($hours = 24)
    {
        return !$this->isExpired($hours);
    }

    /**
     * Generate a new reset token.
     */
    public static function generateToken()
    {
        return \Illuminate\Support\Str::random(64);
    }

    /**
     * Create a new password reset token record.
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
