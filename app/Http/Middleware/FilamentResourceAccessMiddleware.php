<?php

namespace App\Http\Middleware;

use App\Services\NavigationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentResourceAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        // Extract resource name from route
        $routeName = $request->route()->getName();
        $resourceName = $this->extractResourceName($routeName);

        if ($resourceName && !NavigationService::canAccessResource($resourceName)) {
            abort(403, 'Access denied to this resource');
        }

        return $next($request);
    }

    private function extractResourceName(string $routeName): ?string
    {
        // Extract resource name from Filament route names
        // e.g., "filament.admin.resources.products.index" -> "ProductResource"
        if (preg_match('/filament\.admin\.resources\.([^.]+)\./', $routeName, $matches)) {
            return ucfirst($matches[1]) . 'Resource';
        }

        return null;
    }
}