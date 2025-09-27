<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $key = 'api', int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $identifier = $this->resolveRequestSignature($request, $key);

        if (RateLimiter::tooManyAttempts($identifier, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($identifier);
            
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Too many requests. Please try again later.',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'version' => 'v1',
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                    'retry_after' => $retryAfter,
                ]
            ], 429, [
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        RateLimiter::hit($identifier, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $remaining = RateLimiter::remaining($identifier, $maxAttempts);
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remaining,
        ]);

        return $response;
    }

    /**
     * Resolve the rate limiting signature for the request.
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        $user = $request->user();
        
        if ($user) {
            return $key . '|user:' . $user->id;
        }

        return $key . '|ip:' . $request->ip();
    }
}
