<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpAddress extends Model
{
    use HasFactory, SoftDeletes;

    public const ALLOWED_FILTERS = ['address', 'label'];
    public const ALLOWED_SORTS = ['created_at', 'label'];

    protected $fillable = [
        'user_id',
        'address',
        'label',
        'comment',
    ];

    public function scopeFilter($query, $filters)
    {
        return $query
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
}
