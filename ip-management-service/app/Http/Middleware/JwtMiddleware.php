<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Authorization token not found.'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        // dd($token);
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                throw new \Exception('Malformed token.');
            }

            $payload = json_decode(base64_decode($parts[1]), true);

            if (!$payload || !isset($payload['sub'], $payload['role'])) {
                throw new \Exception('Invalid token payload.');
            }

            // Attach decoded values to the request
            $request->merge([
                'user_id' => $payload['sub'],
                'role' => $payload['role'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token. ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
