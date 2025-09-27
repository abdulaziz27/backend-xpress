<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\SendQuotaWarningNotification;

class PlanGateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature
     * @param  string|null  $limit
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $feature, string $limit = null)
    {
        $user = auth()->user();
        
        if (!$user || !$user->store_id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NO_STORE_CONTEXT',
                    'message' => 'User must be associated with a store',
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                ],
            ], 403);
        }
        
        $store = $user->store;
        $subscription = $store->activeSubscription;
        
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NO_ACTIVE_SUBSCRIPTION',
                    'message' => 'Store has no active subscription',
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                ],
            ], 403);
        }
        
        if ($subscription->hasExpired()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SUBSCRIPTION_EXPIRED',
                    'message' => 'Subscription has expired',
                    'expired_at' => $subscription->ends_at->toISOString(),
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                ],
            ], 403);
        }
        
        $plan = $subscription->plan;
        
        // Check feature access
        if (!$plan->hasFeature($feature)) {
            $requiredPlan = $plan->getRequiredPlanFor($feature);
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PLAN_FEATURE_REQUIRED',
                    'message' => "This feature requires {$requiredPlan} plan or higher",
                    'current_plan' => $plan->name,
                    'required_plan' => $requiredPlan,
                    'feature' => $feature,
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                ],
            ], 403);
        }
        
        // Check hard limits (products, users, outlets)
        if ($limit && $this->hasExceededHardLimit($store, $feature, (int)$limit)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PLAN_LIMIT_EXCEEDED',
                    'message' => "You have reached the {$feature} limit for your plan",
                    'current_usage' => $store->getCurrentUsage($feature),
                    'plan_limit' => $plan->getLimit($feature),
                    'feature' => $feature,
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                ],
            ], 403);
        }
        
        // Check premium feature access when over quota
        if ($this->hasExceededTransactionQuota($store) && $this->isPremiumFeature($feature)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'QUOTA_EXCEEDED_PREMIUM_BLOCKED',
                    'message' => 'Premium features are limited when transaction quota is exceeded. Please upgrade your plan.',
                    'feature' => $feature,
                    'quota_status' => $this->getQuotaStatus($store),
                ],
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'request_id' => $request->header('X-Request-ID', uniqid()),
                ],
            ], 403);
        }
        
        // Check transaction soft cap
        if ($feature === 'transactions' && $this->hasExceededTransactionQuota($store)) {
            // Continue processing but add warning headers
            $response = $next($request);
            $response->headers->set('X-Quota-Warning', 'Annual transaction quota exceeded');
            $response->headers->set('X-Upgrade-Recommended', 'true');
            
            // Trigger soft cap notification (async)
            dispatch(new SendQuotaWarningNotification($store));
            
            return $response;
        }
        
        // Process the request
        $response = $next($request);
        
        // Add warning headers if approaching limits
        if ($this->shouldTriggerSoftCapWarning($store, $feature)) {
            $response->headers->set('X-Usage-Warning', 'Approaching plan limits');
            $response->headers->set('X-Usage-Percentage', $this->getUsagePercentage($store, $feature));
        }
        
        // Add quota warning headers if over transaction quota
        if ($this->hasExceededTransactionQuota($store)) {
            $response->headers->set('X-Quota-Warning', 'Annual transaction quota exceeded');
            $response->headers->set('X-Upgrade-Recommended', 'true');
        }
        
        return $response;
    }
    
    /**
     * Check if store has exceeded hard limit for a feature.
     */
    private function hasExceededHardLimit($store, string $feature, int $limit): bool
    {
        $currentUsage = $store->getCurrentUsage($feature);
        return $currentUsage >= $limit;
    }
    
    /**
     * Check if store has exceeded transaction quota.
     */
    private function hasExceededTransactionQuota($store): bool
    {
        return $store->hasExceededTransactionQuota();
    }
    
    /**
     * Check if feature is a premium feature that should be blocked when over quota.
     */
    private function isPremiumFeature(string $feature): bool
    {
        $premiumFeatures = [
            'report_export',
            'advanced_analytics',
            'monthly_email_reports',
        ];
        
        return in_array($feature, $premiumFeatures);
    }
    
    /**
     * Get quota status for a store.
     */
    private function getQuotaStatus($store): array
    {
        $subscription = $store->activeSubscription;
        
        if (!$subscription) {
            return ['status' => 'no_subscription'];
        }
        
        $usage = $subscription->usage()->where('feature_type', 'transactions')->first();
        
        if (!$usage || !$usage->annual_quota) {
            return ['status' => 'unlimited'];
        }
        
        return [
            'status' => 'exceeded',
            'current_usage' => $usage->current_usage,
            'annual_quota' => $usage->annual_quota,
            'percentage' => $usage->getUsagePercentage(),
            'soft_cap_triggered' => $usage->soft_cap_triggered,
        ];
    }
    
    /**
     * Check if soft cap warning should be triggered.
     */
    private function shouldTriggerSoftCapWarning($store, string $feature): bool
    {
        $subscription = $store->activeSubscription;
        
        if (!$subscription) {
            return false;
        }
        
        $plan = $subscription->plan;
        $limit = $plan->getLimit($feature);
        
        if (!$limit) {
            return false; // Unlimited
        }
        
        $currentUsage = $store->getCurrentUsage($feature);
        $usagePercentage = ($currentUsage / $limit) * 100;
        
        return $usagePercentage >= 80 && $usagePercentage < 100;
    }
    
    /**
     * Get usage percentage for a feature.
     */
    private function getUsagePercentage($store, string $feature): float
    {
        $subscription = $store->activeSubscription;
        
        if (!$subscription) {
            return 0;
        }
        
        $plan = $subscription->plan;
        $limit = $plan->getLimit($feature);
        
        if (!$limit) {
            return 0; // Unlimited
        }
        
        $currentUsage = $store->getCurrentUsage($feature);
        return ($currentUsage / $limit) * 100;
    }
}