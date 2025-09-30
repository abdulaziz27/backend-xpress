<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set request to expect JSON response for API routes
        if ($this->isApiRoute($request)) {
            $request->headers->set('Accept', 'application/json');
        }
        
        return $next($request);
    }
    
    /**
     * Check if the request is for an API route
     */
    private function isApiRoute(Request $request): bool
    {
        return $request->is('api/*') || 
               $request->expectsJson() || 
               $request->header('Accept') === 'application/json' ||
               $request->header('Content-Type') === 'application/json';
    }
}