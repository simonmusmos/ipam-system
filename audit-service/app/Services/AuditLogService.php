<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogService
{
    public function fetch(array $filters, int $perPage = 10)
    {
        return AuditLog::query()
            ->filter($filters)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function store(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}