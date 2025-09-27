# Multi-Tenancy Implementation

This document describes the multi-tenancy enforcement system implemented in POS Xpress, which ensures strict data isolation between stores while allowing system administrators to manage the platform globally.

## Overview

The multi-tenancy system provides:
- **Automatic data scoping** to current user's store
- **Cross-store access prevention** with security logging
- **Store switching functionality** for system administrators
- **Comprehensive audit trail** for all tenant-related activities

## Components

### 1. Global Scopes

#### StoreScope (`app/Models/Scopes/StoreScope.php`)

Automatically filters all queries to the current user's store:

```php
// System admin bypasses tenant scoping
if ($user && $user->hasRole('admin_sistem')) {
    return;
}

// Apply store scoping for all other users
if ($user && $user->store_id) {
    $builder->where($model->getTable() . '.store_id', $user->store_id);
} else {
    // Prevent data leakage in edge cases
    $builder->whereRaw('1 = 0');
}
```

**Features:**
- Automatic query filtering by store_id
- System admin bypass functionality
- Security fallback for users without store_id
- Query builder extensions (`withoutStoreScope`, `forStore`, `forAllStores`)

### 2. Model Trait

#### BelongsToStore (`app/Models/Concerns/BelongsToStore.php`)

Provides consistent store scoping behavior across models:

```php
use App\Models\Concerns\BelongsToStore;

class Product extends Model
{
    use BelongsToStore;
}
```

**Features:**
- Automatic StoreScope application
- Auto-assignment of store_id on creation
- Helper methods for store validation
- Consistent store relationship

**Helper Methods:**
- `belongsToStore($storeId)` - Check if model belongs to specific store
- `belongsToCurrentUserStore()` - Check if model belongs to current user's store
- `scopeForStore($query, $storeId)` - Query scope for specific store

### 3. Tenant Scope Middleware

#### TenantScopeMiddleware (`app/Http/Middleware/TenantScopeMiddleware.php`)

Validates and logs cross-store access attempts:

```php
Route::middleware(['auth:sanctum', 'tenant.scope'])->group(function () {
    // Protected routes
});
```

**Features:**
- Route parameter validation
- Model access validation
- Security violation logging
- Activity log creation

**Security Checks:**
- Store parameter validation in routes
- store_id validation in request data
- Model ownership validation
- Cross-store access attempt logging

### 4. Store Switching Service

#### StoreSwitchingService (`app/Services/StoreSwitchingService.php`)

Enables system administrators to switch between store contexts:

```php
$storeSwitchingService = new StoreSwitchingService();

// Switch to specific store
$storeSwitchingService->switchStore($systemAdmin, $storeId);

// Clear context (return to global view)
$storeSwitchingService->clearStoreContext($systemAdmin);
```

**Features:**
- Store context switching for system admins
- Session-based context management
- Activity logging for all switches
- Store validation and access control

### 5. API Endpoints

#### Store Switching Controller (`app/Http/Controllers/Api/V1/StoreSwitchController.php`)

Provides REST API for store switching:

```bash
# Get available stores
GET /api/v1/admin/stores/

# Switch to store
POST /api/v1/admin/stores/switch
{
    "store_id": "store-uuid"
}

# Clear context
POST /api/v1/admin/stores/clear

# Get current context
GET /api/v1/admin/stores/current
```

## Usage Examples

### 1. Model Usage

```php
// Models automatically scope to current user's store
$products = Product::all(); // Only current store's products

// System admin can access all stores
$allProducts = Product::withoutStoreScope()->get();

// Query specific store
$storeProducts = Product::forStore($storeId)->get();

// Create model (store_id auto-assigned)
$product = Product::create([
    'name' => 'New Product',
    'price' => 10.00,
    // store_id automatically set
]);
```

### 2. Store Switching (System Admin)

```php
// Check if user can switch stores
if ($user->hasRole('admin_sistem')) {
    // Get available stores
    $stores = $storeSwitchingService->getAvailableStores($user);
    
    // Switch to specific store
    $storeSwitchingService->switchStore($user, $storeId);
    
    // Now all queries are scoped to the selected store
    $products = Product::all(); // Products from selected store
    
    // Clear context to return to global view
    $storeSwitchingService->clearStoreContext($user);
}
```

### 3. API Usage

```javascript
// Switch store context
const response = await fetch('/api/v1/admin/stores/switch', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        store_id: 'store-uuid'
    })
});

// Get current context
const context = await fetch('/api/v1/admin/stores/current', {
    headers: {
        'Authorization': 'Bearer ' + token,
    }
});
```

## Security Features

### 1. Data Isolation

- **Automatic Scoping**: All queries automatically filtered by store_id
- **Creation Protection**: Models automatically assigned to current user's store
- **Cross-Store Prevention**: Middleware blocks access to other stores' data

### 2. Security Logging

All security violations are logged with:
- User information
- Attempted access details
- IP address and user agent
- Timestamp and violation type

```php
Log::critical('Security violation detected', [
    'violation_type' => 'cross_store_access_attempt',
    'user_id' => $user->id,
    'user_store_id' => $user->store_id,
    'requested_store_id' => $requestedStoreId,
    'ip_address' => $request->ip(),
    'timestamp' => now()->toISOString(),
]);
```

### 3. Activity Logging

All store switching activities are logged:
- Store context switches
- Context clearing
- Failed access attempts
- Administrative actions

## Role-Based Access

### System Administrator (`admin_sistem`)
- **Global Access**: Can access all stores' data
- **Store Switching**: Can switch between store contexts
- **Bypass Scoping**: Automatically bypasses tenant scoping
- **Audit Trail**: All actions logged for compliance

### Store Owner (`owner`)
- **Store Scoped**: Access limited to their store only
- **Staff Management**: Can manage staff within their store
- **No Switching**: Cannot switch to other stores

### Other Roles (`manager`, `cashier`, etc.)
- **Store Scoped**: Access limited to their assigned store
- **Role Permissions**: Additional restrictions based on role
- **No Administrative Access**: Cannot manage store context

## Testing

Comprehensive test coverage includes:

### Multi-Tenancy Tests (`tests/Feature/MultiTenancyEnforcementTest.php`)
- Data isolation verification
- Scope bypass functionality
- Security violation detection
- Model behavior validation

### Store Switching Tests (`tests/Feature/StoreSwitchingTest.php`)
- Store switching functionality
- API endpoint testing
- Permission validation
- Activity logging verification

## Configuration

### Middleware Registration

```php
// app/Http/Kernel.php
protected $middlewareAliases = [
    'tenant.scope' => \App\Http\Middleware\TenantScopeMiddleware::class,
];
```

### Route Protection

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'tenant.scope'])->group(function () {
    // All routes automatically protected
});
```

### Model Setup

```php
// Apply to all store-scoped models
use App\Models\Concerns\BelongsToStore;

class YourModel extends Model
{
    use BelongsToStore;
    
    protected $fillable = [
        'store_id', // Required field
        // other fields...
    ];
}
```

## Best Practices

### 1. Model Implementation
- Always use `BelongsToStore` trait for store-scoped models
- Include `store_id` in fillable arrays
- Test model scoping behavior

### 2. Controller Implementation
- Apply `tenant.scope` middleware to protected routes
- Validate store ownership in controllers
- Handle cross-store access gracefully

### 3. Database Design
- Include `store_id` foreign key in all tenant tables
- Add proper indexes for performance
- Use UUIDs for store identifiers

### 4. Security Considerations
- Monitor security violation logs
- Implement rate limiting for sensitive operations
- Regular audit of access patterns
- Test edge cases and error conditions

## Troubleshooting

### Common Issues

1. **User without store_id gets no results**
   - Solution: Ensure all users have valid store_id assigned
   - Check: User creation process assigns store_id

2. **System admin sees scoped data**
   - Solution: Verify user has `admin_sistem` role
   - Check: Role assignment and permissions

3. **Cross-store access not blocked**
   - Solution: Ensure `tenant.scope` middleware is applied
   - Check: Route middleware configuration

4. **Store switching not working**
   - Solution: Verify user has system admin role
   - Check: Session configuration and storage

### Debugging

```php
// Check current user's store context
$user = auth()->user();
$storeId = $user->store_id;
$isAdmin = $user->hasRole('admin_sistem');

// Check if model has store scope
$query = Product::toSql(); // Should include store_id WHERE clause

// Test scope bypass
$allProducts = Product::withoutStoreScope()->get();
```

## Migration Guide

When adding multi-tenancy to existing models:

1. **Add store_id column** to existing tables
2. **Update model** to use BelongsToStore trait
3. **Populate store_id** for existing records
4. **Add middleware** to routes
5. **Test thoroughly** with different user roles

```sql
-- Add store_id to existing table
ALTER TABLE existing_table 
ADD COLUMN store_id CHAR(36) AFTER id,
ADD INDEX idx_store_id (store_id),
ADD FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE;
```

This multi-tenancy implementation ensures secure, scalable data isolation while providing the flexibility needed for system administration and store management.