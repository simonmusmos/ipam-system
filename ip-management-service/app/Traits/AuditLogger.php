<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

trait AuditLogger
{
    public function logAudit(Request $request, string $action, string $model = null, int $modelId = null): void
    {
        try {
            Http::withHeaders([
                'X-Internal-Key' => config('services.audit.internal_key'),
            ])->post(config('services.audit.url') . '/api/audit', [
                'user_id' => $request->user_id ?? auth()->id(),
                'action' => $action,
                'model' => $model,
                'model_id' => $modelId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'path' => $request->path(),
            ]);
        } catch (\Throwable $e) {
            // Optional: log error but don't interrupt main flow
            \Log::warning('Audit logging failed: ' . $e->getMessage());
        }
    }
}