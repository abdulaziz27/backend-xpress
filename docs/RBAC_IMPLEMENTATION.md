# Role-Based Access Control (RBAC) Implementation

## Overview

This document describes the implementation of the Role-Based Access Control (RBAC) system for POS Xpress, which provides multi-level authentication and authorization with granular permissions.

## Roles and Permissions

### Default Roles

| Role | Scope | Description | Access Level |
|------|-------|-------------|--------------|
| **admin_sistem** | Global | System administrator with full access | Bypasses all tenant scoping |
| **owner** | Store-scoped | Store owner with full store management | Full access within their store |
| **manager** | Store-scoped | Store manager with operational access | Limited management permissions |
| **cashier** | Store-scoped | Point-of-sale operator | Basic POS operations only |

### Permission Categories

#### User Management
- `users.view` - View user information
- `users.create` - Create new users
- `users.update` - Update user information
- `users.delete` - Delete users
- `users.manage_roles` - Assign/remove roles

#### Product Management
- `products.view` - View products
- `products.create` - Create new products
- `products.update` - Update product information
- `products.delete` - Delete products
- `products.manage_categories` - Manage product categories

#### Order Management
- `orders.view` - View orders
- `orders.create` - Create new orders
- `orders.update` - Update order information
- `orders.delete` - Delete orders
- `orders.refund` - Process refunds
- `orders.void` - Void orders

#### Inventory Management
- `inventory.view` - View inventory levels
- `inventory.adjust` - Adjust stock levels
- `inventory.transfer` - Transfer stock between locations
- `inventory.reports` - View inventory reports

#### Reports
- `reports.view` - View reports
- `reports.export` - Export reports
- `reports.email` - Email reports

#### Cash Sessions
- `cash_sessions.open` - Open cash sessions
- `cash_sessions.close` - Close cash sessions
- `cash_sessions.view` - View cash session data
- `cash_sessions.manage` - Manage cash sessions

#### System Management (admin_sistem only)
- `subscription.view` - View subscription information
- `subscription.manage` - Manage subscriptions
- `system.backup` - Perform system backups
- `system.maintenance` - System maintenance operations
- `system.logs` - Access system logs

## Implementation Components

### 1. Middleware

#### PermissionMiddleware
- **File**: `app/Http/Middleware/PermissionMiddleware.php`
- **Usage**: `Route::middleware('permission:products.view')`
- **Purpose**: Validates that the authenticated user has the required permission
- **System Admin Bypass**: System admins bypass all permission checks

#### RoleMiddleware
- **File**: `app/Http/Middleware/RoleMiddleware.php`
- **Usage**: `Route::middleware('role:owner,manager')`
- **Purpose**: Validates that the authenticated user has one of the required roles

### 2. Policies

#### UserPolicy
- **File**: `app/Policies/UserPolicy.php`
- **Purpose**: Authorizes user management operations
- **Key Features**:
  - System admin can manage any user
  - Store owners can manage users in their store
  - Users can view/update themselves (limited)
  - Prevents deletion of system admins by store owners

#### ProductPolicy
- **File**: `app/Policies/ProductPolicy.php`
- **Purpose**: Authorizes product management operations
- **Key Features**:
  - Enforces store-scoped access
  - System admin has global access

#### OrderPolicy
- **File**: `app/Policies/OrderPolicy.php`
- **Purpose**: Authorizes order management operations
- **Key Features**:
  - Store-scoped access control
  - Different permissions for different operations (view, create, refund, void)

#### CategoryPolicy
- **File**: `app/Policies/CategoryPolicy.php`
- **Purpose**: Authorizes category management operations

### 3. Staff Management API

#### StaffController
- **File**: `app/Http/Controllers/Api/V1/StaffController.php`
- **Access**: Owner role only
- **Features**:
  - CRUD operations for staff members
  - Role assignment/removal
  - Permission granting/revoking
  - Store-scoped access enforcement

#### API Endpoints

```php
// Staff Management (Owner only)
GET    /api/v1/staff                           // List staff members
POST   /api/v1/staff                           // Create staff member
GET    /api/v1/staff/{id}                      // View staff member
PUT    /api/v1/staff/{id}                      // Update staff member
DELETE /api/v1/staff/{id}                      // Delete staff member

// Role Management
POST   /api/v1/staff/{id}/roles                // Assign role
DELETE /api/v1/staff/{id}/roles/{role}         // Remove role

// Permission Management
POST   /api/v1/staff/{id}/permissions          // Grant permission
DELETE /api/v1/staff/{id}/permissions/{perm}   // Revoke permission

// Utility Endpoints
GET    /api/v1/roles/available                 // Get available roles
GET    /api/v1/permissions/available           // Get available permissions
```

## Multi-Tenancy Enforcement

### Store Scoping
- All models (except system-level) include `store_id`
- Global scopes automatically filter queries by store
- System admin role bypasses tenant scoping

### Cross-Store Access Prevention
- Policies validate store ownership
- API controllers enforce store-scoped access
- Security violations are logged

## Usage Examples

### Route Protection with Middleware

```php
// Require specific permission
Route::middleware('permission:products.create')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
});

// Require specific role
Route::middleware('role:owner,manager')->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});

// Combine multiple middleware
Route::middleware(['auth:sanctum', 'permission:orders.refund'])->group(function () {
    Route::post('/orders/{order}/refund', [OrderController::class, 'refund']);
});
```

### Policy Usage in Controllers

```php
class ProductController extends Controller
{
    public function show(Product $product)
    {
        $this->authorize('view', $product);
        
        return response()->json($product);
    }
    
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        // Update logic here
    }
}
```

### Manual Permission Checking

```php
// Check if user has permission
if (auth()->user()->can('products.create')) {
    // Allow product creation
}

// Check if user has role
if (auth()->user()->hasRole('owner')) {
    // Owner-specific logic
}

// Check if system admin
if (auth()->user()->hasRole('admin_sistem')) {
    // System admin logic
}
```

## Security Features

### System Admin Protection
- System admins cannot be managed by store owners
- System admin role cannot be assigned by store owners
- System permissions cannot be granted by store owners

### Store Isolation
- All data access is automatically scoped to user's store
- Cross-store access attempts are blocked and logged
- System admins can access all stores

### Permission Validation
- All API endpoints validate required permissions
- Middleware provides consistent authorization
- Policies handle model-specific authorization

## Testing

### Test Coverage
- **Feature Tests**: `tests/Feature/Api/RoleBasedAccessControlTest.php`
- **Unit Tests**: 
  - `tests/Unit/PermissionMiddlewareTest.php`
  - `tests/Unit/RoleMiddlewareTest.php`

### Test Scenarios
- System admin global access
- Store owner scoped access
- Role-based permission enforcement
- Cross-store access prevention
- Staff management API functionality
- Middleware authorization logic

## Configuration

### Middleware Registration
Middleware is registered in `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

### Policy Registration
Policies are registered in `app/Providers/AuthServiceProvider.php`:

```php
protected $policies = [
    \App\Models\User::class => \App\Policies\UserPolicy::class,
    \App\Models\Product::class => \App\Policies\ProductPolicy::class,
    \App\Models\Order::class => \App\Policies\OrderPolicy::class,
    \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
];
```

## Database Seeding

Run the role and permission seeder to set up the default roles and permissions:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

This creates all the default roles and assigns appropriate permissions to each role.