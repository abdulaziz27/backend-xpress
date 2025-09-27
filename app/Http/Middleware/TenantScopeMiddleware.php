<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TenantScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // System admin bypasses tenant scoping
        if ($user->hasRole('admin_sistem')) {
            return $next($request);
        }

        // Validate store access for route parameters
        $this->validateStoreAccess($request, $user);

        // Log cross-store access attempts
        $this->logCrossStoreAccess($request, $user);

        return $next($request);
    }

    /**
     * Validate store access for route parameters.
     */
    private function validateStoreAccess(Request $request, $user): void
    {
        // Check if route has store parameter
        if ($request->route('store') && $request->route('store') !== $user->store_id) {
            $this->logSecurityViolation($request, $user, 'cross_store_access_attempt');
            abort(403, 'Access denied to this store');
        }

        // Check for store_id in request data
        if ($request->has('store_id') && $request->input('store_id') !== $user->store_id) {
            $this->logSecurityViolation($request, $user, 'cross_store_data_access');
            abort(403, 'Cannot access data from different store');
        }

        // Validate any model IDs in the route belong to user's store
        $this->validateModelAccess($request, $user);
    }

    /**
     * Validate that model IDs in route belong to user's store.
     */
    private function validateModelAccess(Request $request, $user): void
    {
        $routeParameters = $request->route()->parameters();
        
        foreach ($routeParameters as $key => $value) {
            // Skip non-model parameters
            if (in_array($key, ['store', 'id']) || !is_string($value)) {
                continue;
            }

            // Map route parameter names to model classes
            $modelClass = $this->getModelClassFromParameter($key);
            
            if ($modelClass && class_exists($modelClass)) {
                $model = $modelClass::withoutGlobalScopes()->find($value);
                
                if ($model && isset($model->store_id) && $model->store_id !== $user->store_id) {
                    $this->logSecurityViolation($request, $user, 'cross_store_model_access', [
                        'model_type' => $modelClass,
                        'model_id' => $value,
                        'model_store_id' => $model->store_id
                    ]);
                    abort(403, 'Access denied to resource from different store');
                }
            }
        }
    }

    /**
     * Get model class from route parameter name.
     */
    private function getModelClassFromParameter(string $parameter): ?string
    {
        $modelMap = [
            'product' => \App\Models\Product::class,
            'category' => \App\Models\Category::class,
            'order' => \App\Models\Order::class,
            'user' => \App\Models\User::class,
            'member' => \App\Models\Member::class,
            'table' => \App\Models\Table::class,
            'payment' => \App\Models\Payment::class,
            'refund' => \App\Models\Refund::class,
            'expense' => \App\Models\Expense::class,
            'cash_session' => \App\Models\CashSession::class,
        ];

        return $modelMap[$parameter] ?? null;
    }

    /**
     * Log cross-store access attempts for monitoring.
     */
    private function logCrossStoreAccess(Request $request, $user): void
    {
        // Only log if there are suspicious patterns
        $suspiciousPatterns = [
            'store_id' => $request->has('store_id') && $request->input('store_id') !== $user->store_id,
            'different_store_route' => $request->route('store') && $request->route('store') !== $user->store_id,
        ];

        if (array_filter($suspiciousPatterns)) {
            Log::warning('Cross-store access attempt detected', [
                'user_id' => $user->id,
                'user_store_id' => $user->store_id,
                'requested_store_id' => $request->input('store_id') ?? $request->route('store'),
                'route' => $request->route()->getName(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'patterns' => $suspiciousPatterns,
            ]);
        }
    }

    /**
     * Log security violations.
     */
    private function logSecurityViolation(Request $request, $user, string $violationType, array $additionalData = []): void
    {
        Log::critical('Security violation detected', array_merge([
            'violation_type' => $violationType,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_store_id' => $user->store_id,
            'route' => $request->route()->getName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ], $additionalData));

        // Create activity log entry for audit trail
        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'store_id' => $user->store_id,
                'user_id' => $user->id,
                'event' => 'security.violation',
                'auditable_type' => 'security_violation',
                'auditable_id' => null,
                'old_values' => null,
                'new_values' => [
                    'violation_type' => $violationType,
                    'additional_data' => $additionalData,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
    }
}