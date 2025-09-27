<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'Authentication required'
                ]
            ], 401);
        }

        $user = auth()->user();

        // System admin bypasses all permission checks
        if ($user->hasRole('admin_sistem')) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$user->can($permission)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Insufficient permissions to perform this action',
                    'required_permission' => $permission
                ]
            ], 403);
        }

        return $next($request);
    }
}