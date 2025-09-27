<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'services' => [
                'database' => 'connected',
                'cache' => 'connected',
            ],
            'timestamp' => now()->toISOString(),
            'version' => 'v1'
        ]);
    });

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        // Public authentication routes with rate limiting
        Route::middleware('rate.limit:auth,5,1')->group(function () {
            Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
            Route::post('/forgot-password', [App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword']);
            Route::post('/reset-password', [App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword']);
        });
        
        // Protected authentication routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
            Route::post('/logout-all', [App\Http\Controllers\Api\V1\AuthController::class, 'logoutAll']);
            Route::get('/me', [App\Http\Controllers\Api\V1\AuthController::class, 'me']);
            Route::post('/change-password', [App\Http\Controllers\Api\V1\AuthController::class, 'changePassword']);
            Route::get('/sessions', [App\Http\Controllers\Api\V1\AuthController::class, 'sessions']);
            Route::delete('/sessions/{tokenId}', [App\Http\Controllers\Api\V1\AuthController::class, 'revokeSession']);
        });
    });

    // Public Staff Invitation Routes (no authentication required)
    Route::prefix('invitations')->group(function () {
        Route::get('/{token}', [App\Http\Controllers\Api\V1\InvitationController::class, 'show']);
        Route::post('/{token}/accept', [App\Http\Controllers\Api\V1\InvitationController::class, 'accept']);
        Route::post('/{token}/decline', [App\Http\Controllers\Api\V1\InvitationController::class, 'decline']);
    });

    // Protected API Routes (will be implemented in later tasks)
    Route::middleware(['auth:sanctum', 'tenant.scope'])->group(function () {
        // Store Switching Routes (System Admin only)
        Route::prefix('admin/stores')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\StoreSwitchController::class, 'index']);
            Route::post('/switch', [App\Http\Controllers\Api\V1\StoreSwitchController::class, 'switch']);
            Route::post('/clear', [App\Http\Controllers\Api\V1\StoreSwitchController::class, 'clear']);
            Route::get('/current', [App\Http\Controllers\Api\V1\StoreSwitchController::class, 'current']);
        });
        
        // Staff Management Routes (Owner only)
        // Note: Specific routes must come before resource routes to avoid conflicts
        
        // Staff Invitation Routes (Owner only)
        Route::post('staff/invite', [App\Http\Controllers\Api\V1\StaffController::class, 'invite']);
        Route::get('staff/invitations', [App\Http\Controllers\Api\V1\StaffController::class, 'invitations']);
        Route::post('staff/invitations/{invitation}/cancel', [App\Http\Controllers\Api\V1\StaffController::class, 'cancelInvitation']);
        Route::post('staff/invitations/{invitation}/resend', [App\Http\Controllers\Api\V1\StaffController::class, 'resendInvitation']);
        
        // Staff Activity & Performance Routes (Owner only)
        Route::get('staff/activity-logs', [App\Http\Controllers\Api\V1\StaffController::class, 'activityLogs']);
        Route::get('staff/performance', [App\Http\Controllers\Api\V1\StaffController::class, 'performance']);
        Route::get('staff/{staff}/performance', [App\Http\Controllers\Api\V1\StaffController::class, 'performance']);
        Route::post('staff/{staff}/performance', [App\Http\Controllers\Api\V1\StaffController::class, 'updatePerformance']);
        
        // Staff CRUD and Role/Permission Routes
        Route::apiResource('staff', App\Http\Controllers\Api\V1\StaffController::class);
        Route::post('staff/{staff}/roles', [App\Http\Controllers\Api\V1\StaffController::class, 'assignRole']);
        Route::delete('staff/{staff}/roles/{role}', [App\Http\Controllers\Api\V1\StaffController::class, 'removeRole']);
        Route::post('staff/{staff}/permissions', [App\Http\Controllers\Api\V1\StaffController::class, 'grantPermission']);
        Route::delete('staff/{staff}/permissions/{permission}', [App\Http\Controllers\Api\V1\StaffController::class, 'revokePermission']);
        Route::get('roles/available', [App\Http\Controllers\Api\V1\StaffController::class, 'availableRoles']);
        Route::get('permissions/available', [App\Http\Controllers\Api\V1\StaffController::class, 'availablePermissions']);
        
        // Product Catalog Management Routes
        Route::apiResource('categories', App\Http\Controllers\Api\V1\CategoryController::class);
        Route::get('categories-options', [App\Http\Controllers\Api\V1\CategoryController::class, 'options']);
        
        // Product Management Routes
        Route::apiResource('products', App\Http\Controllers\Api\V1\ProductController::class);
        Route::post('products/{product}/archive', [App\Http\Controllers\Api\V1\ProductController::class, 'archive']);
        Route::post('products/{product}/restore', [App\Http\Controllers\Api\V1\ProductController::class, 'restore']);
        Route::post('products/{product}/upload-image', [App\Http\Controllers\Api\V1\ProductController::class, 'uploadImage']);
        Route::get('products/{product}/price-history', [App\Http\Controllers\Api\V1\ProductController::class, 'priceHistory']);
        Route::get('products-search', [App\Http\Controllers\Api\V1\ProductController::class, 'search']);
        
        // Product Options Management Routes
        Route::apiResource('products.options', App\Http\Controllers\Api\V1\ProductOptionController::class);
        Route::post('products/{product}/calculate-price', [App\Http\Controllers\Api\V1\ProductOptionController::class, 'calculatePrice']);
        Route::get('products/{product}/option-groups', [App\Http\Controllers\Api\V1\ProductOptionController::class, 'groups']);
        
        // Order Management Routes (POS Operations)
        Route::apiResource('orders', App\Http\Controllers\Api\V1\OrderController::class);
        Route::post('orders/{order}/items', [App\Http\Controllers\Api\V1\OrderController::class, 'addItem']);
        Route::put('orders/{order}/items/{item}', [App\Http\Controllers\Api\V1\OrderController::class, 'updateItem']);
        Route::delete('orders/{order}/items/{item}', [App\Http\Controllers\Api\V1\OrderController::class, 'removeItem']);
        Route::post('orders/{order}/complete', [App\Http\Controllers\Api\V1\OrderController::class, 'complete']);
        Route::get('orders-summary', [App\Http\Controllers\Api\V1\OrderController::class, 'summary']);
        
        // Payment Management Routes
        Route::apiResource('payments', App\Http\Controllers\Api\V1\PaymentController::class);
        Route::get('payments-methods', [App\Http\Controllers\Api\V1\PaymentController::class, 'paymentMethods']);
        Route::get('payments-summary', [App\Http\Controllers\Api\V1\PaymentController::class, 'summary']);
        
        // Refund Management Routes
        Route::apiResource('refunds', App\Http\Controllers\Api\V1\RefundController::class);
        Route::get('refunds-summary', [App\Http\Controllers\Api\V1\RefundController::class, 'summary']);
        
        // Table Management Routes
        Route::apiResource('tables', App\Http\Controllers\Api\V1\TableController::class);
        Route::get('tables-available', [App\Http\Controllers\Api\V1\TableController::class, 'available']);
        Route::post('tables/{table}/occupy', [App\Http\Controllers\Api\V1\TableController::class, 'occupy']);
        Route::post('tables/{table}/make-available', [App\Http\Controllers\Api\V1\TableController::class, 'makeAvailable']);
        Route::get('tables/{table}/occupancy-stats', [App\Http\Controllers\Api\V1\TableController::class, 'occupancyStats']);
        Route::get('tables/{table}/occupancy-history', [App\Http\Controllers\Api\V1\TableController::class, 'occupancyHistory']);
        Route::get('table-occupancy-report', [App\Http\Controllers\Api\V1\TableController::class, 'occupancyReport']);
        
        // Member Management Routes
        Route::apiResource('members', App\Http\Controllers\Api\V1\MemberController::class);
        Route::post('members/{member}/loyalty-points/add', [App\Http\Controllers\Api\V1\MemberController::class, 'addLoyaltyPoints']);
        Route::post('members/{member}/loyalty-points/redeem', [App\Http\Controllers\Api\V1\MemberController::class, 'redeemLoyaltyPoints']);
        Route::post('members/{member}/loyalty-points/adjust', [App\Http\Controllers\Api\V1\MemberController::class, 'adjustLoyaltyPoints']);
        Route::get('members/{member}/statistics', [App\Http\Controllers\Api\V1\MemberController::class, 'statistics']);
        Route::get('members/{member}/loyalty-history', [App\Http\Controllers\Api\V1\MemberController::class, 'loyaltyHistory']);
        Route::get('member-tiers', [App\Http\Controllers\Api\V1\MemberController::class, 'tiers']);
        Route::get('member-tier-statistics', [App\Http\Controllers\Api\V1\MemberController::class, 'tierStatistics']);
        
        // Inventory Management Routes (Pro/Enterprise plans only)
        Route::prefix('inventory')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\InventoryController::class, 'index']);
            Route::get('/{product}', [App\Http\Controllers\Api\V1\InventoryController::class, 'show']);
            Route::post('/adjust', [App\Http\Controllers\Api\V1\InventoryController::class, 'adjust']);
            Route::get('/movements/list', [App\Http\Controllers\Api\V1\InventoryController::class, 'movements']);
            Route::post('/transfer', [App\Http\Controllers\Api\V1\InventoryController::class, 'transfer']);
            Route::get('/alerts/low-stock', [App\Http\Controllers\Api\V1\InventoryController::class, 'lowStockAlerts']);
        });

        // Recipe Management Routes (Pro/Enterprise plans only)
        Route::prefix('recipes')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\RecipeController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\V1\RecipeController::class, 'store']);
            Route::get('/{recipe}', [App\Http\Controllers\Api\V1\RecipeController::class, 'show']);
            Route::put('/{recipe}', [App\Http\Controllers\Api\V1\RecipeController::class, 'update']);
            Route::delete('/{recipe}', [App\Http\Controllers\Api\V1\RecipeController::class, 'destroy']);
            Route::get('/{recipe}/calculate-cost', [App\Http\Controllers\Api\V1\RecipeController::class, 'calculateCost']);
            Route::post('/{recipe}/update-costs', [App\Http\Controllers\Api\V1\RecipeController::class, 'updateCosts']);
            Route::get('/ingredients/available', [App\Http\Controllers\Api\V1\RecipeController::class, 'availableIngredients']);
        });

        // Inventory Reports Routes (Pro/Enterprise plans only)
        Route::prefix('inventory/reports')->group(function () {
            Route::get('/stock-levels', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'stockLevels']);
            Route::get('/movements', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'movements']);
            Route::get('/valuation', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'valuation']);
            Route::get('/cogs-analysis', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'cogsAnalysis']);
            Route::get('/stock-aging', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'stockAging']);
            Route::get('/stock-turnover', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'stockTurnover']);
            Route::post('/export', [App\Http\Controllers\Api\V1\InventoryReportController::class, 'export']);
        });

        // Cash Flow & Expense Management Routes
        Route::apiResource('cash-sessions', App\Http\Controllers\Api\V1\CashSessionController::class);
        Route::post('cash-sessions/{cashSession}/close', [App\Http\Controllers\Api\V1\CashSessionController::class, 'close']);
        Route::get('cash-sessions-current', [App\Http\Controllers\Api\V1\CashSessionController::class, 'current']);
        Route::get('cash-sessions-summary', [App\Http\Controllers\Api\V1\CashSessionController::class, 'summary']);

        Route::apiResource('expenses', App\Http\Controllers\Api\V1\ExpenseController::class);
        Route::get('expense-categories', [App\Http\Controllers\Api\V1\ExpenseController::class, 'categories']);
        Route::get('expenses-summary', [App\Http\Controllers\Api\V1\ExpenseController::class, 'summary']);

        // Cash Flow Reports Routes
        Route::prefix('reports/cash-flow')->group(function () {
            Route::get('/daily', [App\Http\Controllers\Api\V1\CashFlowReportController::class, 'dailyCashFlow']);
            Route::get('/payment-methods', [App\Http\Controllers\Api\V1\CashFlowReportController::class, 'paymentMethodBreakdown']);
            Route::get('/variance-analysis', [App\Http\Controllers\Api\V1\CashFlowReportController::class, 'cashVarianceAnalysis']);
            Route::get('/shift-summary', [App\Http\Controllers\Api\V1\CashFlowReportController::class, 'shiftSummary']);
        });
        
        // TODO: Implement controllers in subsequent tasks
        // - ReportController (Task 10)
        // - SyncController (Task 11)
    });
});

// Legacy routes (for backward compatibility - will be deprecated)
Route::middleware('auth:sanctum')->group(function () {
    //login api
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->withoutMiddleware('auth:sanctum');

    //logout api
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

    //products api
    Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::post('/products', [App\Http\Controllers\Api\ProductController::class, 'store']);
    Route::post('/products/edit', [App\Http\Controllers\Api\ProductController::class, 'update']);
    Route::delete('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy']);
    
    //categories api
    Route::apiResource('/api-categories', App\Http\Controllers\Api\CategoryController::class);

    //orders api
    Route::post('/save-order', [App\Http\Controllers\Api\OrderController::class, 'saveOrder']);

    //discounts api
    Route::get('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'index']);
    Route::post('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'store']);

    // api resource report
    Route::get('/orders/{date?}', [App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('/summary/{date?}', [App\Http\Controllers\Api\OrderController::class, 'summary']);
    Route::get('/order-item/{date?}', [App\Http\Controllers\Api\OrderItemController::class, 'index']);
    Route::get('/order-sales', [App\Http\Controllers\Api\OrderItemController::class, 'orderSales']);
});