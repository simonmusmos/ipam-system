<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

trait AuditLogger
{
    public function logAudit(Request $request, array $data): void
    {
        $default = [
            'user_id' => $request->user_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'path' => request()->path(),
        ];

        $payload = array_merge($default, $data);
        try {
            Http::withHeaders([
                'X-Internal-Key' => config('services.audit.internal_key'),
            ])->post(config('services.audit.url') . '/api/audit', $payload);
        } catch (\Throwable $e) {
            // Optional: log error but don't interrupt main flow
            \Log::warning('Audit logging failed: ' . $e->getMessage());
        }
    }
}