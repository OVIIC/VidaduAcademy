<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'attempts',
        'last_attempt',
        'locked_until'
    ];

    protected $casts = [
        'last_attempt' => 'datetime',
        'locked_until' => 'datetime'
    ];

    public function isLocked(): bool
    {
        return $this->locked_until && now()->lessThan($this->locked_until);
    }

    public function getRemainingLockTimeAttribute(): ?int
    {
        if (!$this->isLocked()) {
            return null;
        }

        return $this->locked_until->diffInMinutes(now());
    }
}
