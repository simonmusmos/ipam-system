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
        'old_values',
        'new_values',
        'model_description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['user'] ?? null, fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['action'] ?? null, fn($q, $v) => $q->where('action', $v))
            ->when($filters['model'] ?? null, fn($q, $v) => $q->where('model', $v))
            ->when($filters['model_id'] ?? null, fn($q, $v) => $q->where('model_id', $v))
            ->when($filters['start_date'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['end_date'] ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v));
    }

    public function getDescriptionAttribute()
    {
        $action = ucfirst($this->action); // e.g. Create, Delete
        $type = '';
        $model = class_basename($this->model); // e.g. IpAddress

        if ($model === 'IpAddress') {
            $type = "IP Address";

            $data = $this->new_values ?: $this->old_values;
            $address = $data['address'] ?? '';

            return "{$action} {$type} {$this->model_description}";
        }

        return "{$action}";
    }

    public function getChangesSummaryAttribute()
    {
        if (!$this->old_values || !$this->new_values) {
            return null;
        }

        $old = is_array($this->old_values) ? $this->old_values : json_decode($this->old_values, true);
        $new = is_array($this->new_values) ? $this->new_values : json_decode($this->new_values, true);

        $summary = [];

        foreach ($new as $key => $newValue) {
            if (isset($old[$key]) && $old[$key] != $newValue) {
                $summary[] = "{$key}: \"{$old[$key]}\" → \"{$newValue}\"";
            }
        }

        return implode(', ', $summary);
    }
}
