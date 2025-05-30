<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'ip_address',
        'user_agent',
        'path',
    ];

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['user_id'] ?? null, fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['action'] ?? null, fn($q, $v) => $q->where('action', $v))
            ->when($filters['model'] ?? null, fn($q, $v) => $q->where('model', $v))
            ->when($filters['start_date'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['end_date'] ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
