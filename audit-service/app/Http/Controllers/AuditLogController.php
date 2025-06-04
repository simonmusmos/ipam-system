<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuditLogService;

class AuditLogController extends Controller
{
    protected AuditLogService $auditService;

    public function __construct(AuditLogService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index(Request $request)
    {
        return $this->auditService->fetch(
            $request->only(['user_id', 'action', 'model', 'model_id', 'start_date', 'end_date']),
            $request->input('per_page', config('pagination.per_page'))
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'action' => 'required|string',
            'model' => 'nullable|string',
            'model_id' => 'nullable|integer',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string',
            'path' => 'nullable|string',
            'old_values' => 'nullable|array',
            'new_values' => 'nullable|array',
            'model_description' => 'nullable|string',
        ]);

        return response()->json([
            'data' => $this->auditService->store($validated),
            'message' => 'Logged',
        ], 201);
    }
}