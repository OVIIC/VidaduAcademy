<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuspiciousActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'ip_address',
        'user_agent',
        'user_id',
        'payload',
        'endpoint',
        'method',
        'headers',
        'blocked',
        'risk_score',
        'notes'
    ];

    protected $casts = [
        'headers' => 'array',
        'blocked' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_score', '>=', 7);
    }

    public function scopeBlocked($query)
    {
        return $query->where('blocked', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getRiskLevelAttribute(): string
    {
        if ($this->risk_score >= 8) return 'critical';
        if ($this->risk_score >= 6) return 'high';
        if ($this->risk_score >= 4) return 'medium';
        return 'low';
    }
}
