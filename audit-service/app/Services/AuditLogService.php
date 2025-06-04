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
            ->paginate($perPage)
            ->through(fn($log) => [
                'id' => $log->id,
                'description' => $log->description,
                'changes' => $log->changes_summary,
                'user_id' => $log->user_id,
                'created_at' => $log->created_at->toDateTimeString(),
            ]);
    }

    public function store(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}