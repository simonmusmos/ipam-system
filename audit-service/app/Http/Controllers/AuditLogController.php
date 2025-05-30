<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|integer',
            'action'      => 'required|string',
            'model'       => 'nullable|string',
            'model_id'    => 'nullable|integer',
            'ip_address'  => 'nullable|ip',
            'user_agent'  => 'nullable|string',
            'path'        => 'nullable|string',
        ]);

        AuditLog::create($validated);

        return response()->json(['message' => 'Logged'], 201);
    }
}