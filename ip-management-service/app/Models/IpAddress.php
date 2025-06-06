<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpAddress extends Model
{
    use HasFactory, SoftDeletes;

    public const ALLOWED_FILTERS = ['address', 'label', 'user'];
    public const ALLOWED_SORTS = ['created_at', 'label'];

    protected $fillable = [
        'user_id',
        'address',
        'label',
        'comment',
        'type',
    ];

    public function scopeFilter($query, $filters)
    {
        return $query
            ->when(isset($filters['user']), fn($q) => $q->where('user_id', '=', $filters['user']))
            ->when(isset($filters['address']), fn($q) => $q->where('address', 'like', '%' . $filters['address'] . '%'))
            ->when(isset($filters['label']), fn($q) => $q->where('label', 'like', '%' . $filters['label'] . '%'));
    }

    public function scopeSort($query, $sortBy, $sortOrder = 'desc')
    {
        if (in_array($sortBy, self::ALLOWED_SORTS)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        return $query;
    }

    protected static function booted()
    {
        static::saving(function ($ipAddress) {
            $ip = $ipAddress->address;

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipAddress->type = 'ipv6';
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipAddress->type = 'ipv4';
            }
        });
    }
}
