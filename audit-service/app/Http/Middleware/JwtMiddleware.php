<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Authorization token not found.'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Call auth-service to validate token
            $response = Http::withToken($token)->get('http://auth-service:8000/api/validate-token');

            if ($response->status() !== 200) {
                return response()->json(['error' => 'Token is invalid or expired.'], 401);
            }

            $userData = $response->json();

            // Attach user data to request
            $request->merge([
                'user_id' => $userData['user_id'],
                'role' => $userData['role'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token validation failed. ' . $e->getMessage()], 500);
        }

        return $next($request);
    }
}