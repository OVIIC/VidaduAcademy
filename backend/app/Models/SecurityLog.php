<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'event_type',
        'severity',
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'metadata',
        'resource_type',
        'resource_id',
        'is_suspicious',
        'notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_suspicious' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
