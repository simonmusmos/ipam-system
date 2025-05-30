<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyInternalKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-Internal-Key');

        if ($key !== config('services.audit.internal_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
