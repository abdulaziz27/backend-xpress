# API Testing Guide - Postman Collection

## Environment Setup
**Base URL**: `http://localhost:8000/api/v1`
**Authentication**: Bearer Token (Laravel Sanctum)

## Testing Flow Sequence

### 1. Health Check
```
GET {{base_url}}/health
```

### 2. Authentication

#### Login
```
POST {{base_url}}/auth/login
Content-Type: application/json

{
  "email": "admin@store1.com",
  "password": "password"
}
```
**Save the token from response for all subsequent requests**

#### Get Current User
```
GET {{base_url}}/auth/me
Authorization: Bearer {{token}}
```

### 3. Category Management

#### Create Category
```
POST {{base_url}}/categories
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "name": "Beverages",
  "description": "Hot and cold drinks",
  "is_active": true
}
```

#### List Categories
```
GET {{base_url}}/categories
Authorization: Bearer {{token}}
```

#### Get Category Options (for dropdowns)
```
GET {{base_url}}/categories-options
Authorization: Bearer {{token}}
```

### 4. Product Management

#### Create Product
```
POST {{base_url}}/products
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "category_id": 1,
  "name": "Espresso",
  "description": "Strong coffee shot",
  "price": 25000,
  "cost_price": 7500,
  "sku": "ESP001",
  "barcode": "1234567890123",
  "stock_quantity": 100,
  "min_stock_level": 10,
  "max_stock_level": 200,
  "is_trackable": true,
  "is_active": true
}
```

#### List Products
```
GET {{base_url}}/products
Authorization: Bearer {{token}}
```

#### Search Products
```
GET {{base_url}}/products-search?q=espresso
Authorization: Bearer {{token}}
```

#### Get Product Details
```
GET {{base_url}}/products/1
Authorization: Bearer {{token}}
```

#### Update Product
```
PUT {{base_url}}/products/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "name": "Double Espresso",
  "price": 30000,
  "stock_quantity": 95
}
```

### 5. Product Options Management

#### Create Product Option
```
POST {{base_url}}/products/1/options
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "name": "Large Size",
  "type": "size",
  "price_adjustment": 5000,
  "is_required": false
}
```

#### List Product Options
```
GET {{base_url}}/products/1/options
Authorization: Bearer {{token}}
```

#### Calculate Product Price with Options
```
POST {{base_url}}/products/1/calculate-price
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "options": [1, 2]
}
```

### 6. Table Management

#### Create Table
```
POST {{base_url}}/tables
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "number": "T001",
  "name": "Table 1",
  "capacity": 4,
  "location": "Main dining area",
  "is_active": true
}
```

#### List Tables
```
GET {{base_url}}/tables
Authorization: Bearer {{token}}
```

#### Get Available Tables
```
GET {{base_url}}/tables-available
Authorization: Bearer {{token}}
```

#### Occupy Table
```
POST {{base_url}}/tables/1/occupy
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "customer_count": 2
}
```

#### Make Table Available
```
POST {{base_url}}/tables/1/make-available
Authorization: Bearer {{token}}
```

### 7. Member Management

#### Create Member
```
POST {{base_url}}/members
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+6281234567890",
  "date_of_birth": "1990-01-15"
}
```

#### List Members
```
GET {{base_url}}/members
Authorization: Bearer {{token}}
```

#### Get Member Details
```
GET {{base_url}}/members/1
Authorization: Bearer {{token}}
```

#### Add Loyalty Points
```
POST {{base_url}}/members/1/loyalty-points/add
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "points": 100,
  "reason": "Purchase bonus"
}
```

#### Redeem Loyalty Points
```
POST {{base_url}}/members/1/loyalty-points/redeem
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "points": 50,
  "reason": "Discount redemption"
}
```

### 8. Cash Session Management

#### Open Cash Session
```
POST {{base_url}}/cash-sessions
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "opening_amount": 1000000,
  "notes": "Morning shift opening"
}
```

#### Get Current Session
```
GET {{base_url}}/cash-sessions-current
Authorization: Bearer {{token}}
```

#### List Cash Sessions
```
GET {{base_url}}/cash-sessions
Authorization: Bearer {{token}}
```

### 9. Order Management

#### Create Order
```
POST {{base_url}}/orders
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "member_id": 1,
  "table_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 25000,
      "options": [1]
    }
  ],
  "notes": "Extra hot"
}
```

#### List Orders
```
GET {{base_url}}/orders
Authorization: Bearer {{token}}
```

#### Get Order Details
```
GET {{base_url}}/orders/1
Authorization: Bearer {{token}}
```

#### Add Item to Order
```
POST {{base_url}}/orders/1/items
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "product_id": 2,
  "quantity": 1,
  "unit_price": 30000,
  "notes": "No sugar"
}
```

#### Update Order Item
```
PUT {{base_url}}/orders/1/items/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "quantity": 3,
  "notes": "Extra hot, no sugar"
}
```

#### Complete Order
```
POST {{base_url}}/orders/1/complete
Authorization: Bearer {{token}}
```

#### Get Orders Summary
```
GET {{base_url}}/orders-summary
Authorization: Bearer {{token}}
```

### 10. Payment Management

#### Create Payment
```
POST {{base_url}}/payments
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "order_id": 1,
  "amount": 55000,
  "method": "cash",
  "reference_number": "CASH001"
}
```

#### List Payments
```
GET {{base_url}}/payments
Authorization: Bearer {{token}}
```

#### Get Payment Methods
```
GET {{base_url}}/payments-methods
Authorization: Bearer {{token}}
```

#### Get Payments Summary
```
GET {{base_url}}/payments-summary
Authorization: Bearer {{token}}
```

### 11. Refund Management

#### Create Refund
```
POST {{base_url}}/refunds
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "order_id": 1,
  "payment_id": 1,
  "amount": 25000,
  "reason": "Customer complaint"
}
```

#### List Refunds
```
GET {{base_url}}/refunds
Authorization: Bearer {{token}}
```

### 12. Expense Management

#### Create Expense
```
POST {{base_url}}/expenses
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "category": "supplies",
  "description": "Coffee beans purchase",
  "amount": 450000,
  "expense_date": "2024-01-15"
}
```

#### List Expenses
```
GET {{base_url}}/expenses
Authorization: Bearer {{token}}
```

#### Get Expense Categories
```
GET {{base_url}}/expense-categories
Authorization: Bearer {{token}}
```

#### Get Expenses Summary
```
GET {{base_url}}/expenses-summary
Authorization: Bearer {{token}}
```

### 13. Close Cash Session

#### Close Cash Session
```
POST {{base_url}}/cash-sessions/1/close
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "closing_amount": 2500000,
  "notes": "End of shift"
}
```

### 14. Inventory Management (Pro/Enterprise)

#### Get Inventory Levels
```
GET {{base_url}}/inventory
Authorization: Bearer {{token}}
```

#### Get Product Inventory
```
GET {{base_url}}/inventory/1
Authorization: Bearer {{token}}
```

#### Adjust Stock Level
```
POST {{base_url}}/inventory/adjust
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 50,
  "type": "adjustment",
  "notes": "Physical count adjustment"
}
```

#### Get Inventory Movements
```
GET {{base_url}}/inventory/movements/list
Authorization: Bearer {{token}}
```

#### Get Low Stock Alerts
```
GET {{base_url}}/inventory/alerts/low-stock
Authorization: Bearer {{token}}
```

### 15. Recipe Management (Pro/Enterprise)

#### Create Recipe
```
POST {{base_url}}/recipes
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "product_id": 1,
  "name": "Espresso Recipe",
  "description": "Standard espresso preparation",
  "yield_quantity": 1,
  "preparation_time": 2,
  "cooking_time": 0,
  "instructions": {
    "steps": [
      "Grind 18g coffee beans",
      "Extract for 25-30 seconds"
    ]
  },
  "ingredients": [
    {
      "ingredient_id": 5,
      "quantity": 18,
      "unit": "grams"
    }
  ]
}
```

#### List Recipes
```
GET {{base_url}}/recipes
Authorization: Bearer {{token}}
```

#### Get Available Ingredients
```
GET {{base_url}}/recipes/ingredients/available
Authorization: Bearer {{token}}
```

#### Calculate Recipe Cost
```
GET {{base_url}}/recipes/1/calculate-cost
Authorization: Bearer {{token}}
```

### 16. Inventory Reports (Pro/Enterprise)

#### Stock Levels Report
```
GET {{base_url}}/inventory/reports/stock-levels
Authorization: Bearer {{token}}
```

#### Inventory Movements Report
```
GET {{base_url}}/inventory/reports/movements?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer {{token}}
```

#### Inventory Valuation Report
```
GET {{base_url}}/inventory/reports/valuation
Authorization: Bearer {{token}}
```

#### COGS Analysis Report
```
GET {{base_url}}/inventory/reports/cogs-analysis?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer {{token}}
```

### 17. Cash Flow Reports

#### Daily Cash Flow Report
```
GET {{base_url}}/reports/cash-flow/daily?date=2024-01-15
Authorization: Bearer {{token}}
```

#### Payment Methods Breakdown
```
GET {{base_url}}/reports/cash-flow/payment-methods?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer {{token}}
```

#### Cash Variance Analysis
```
GET {{base_url}}/reports/cash-flow/variance-analysis?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer {{token}}
```

#### Shift Summary Report
```
GET {{base_url}}/reports/cash-flow/shift-summary?date=2024-01-15
Authorization: Bearer {{token}}
```

### 18. Staff Management (Owner Only)

#### Invite Staff
```
POST {{base_url}}/staff/invite
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "email": "staff@example.com",
  "role": "cashier",
  "permissions": ["pos.access", "orders.create"]
}
```

#### List Staff
```
GET {{base_url}}/staff
Authorization: Bearer {{token}}
```

#### Get Available Roles
```
GET {{base_url}}/roles/available
Authorization: Bearer {{token}}
```

### 19. Logout

#### Logout
```
POST {{base_url}}/auth/logout
Authorization: Bearer {{token}}
```

## Testing Notes

1. **Follow the sequence** - Some endpoints depend on data from previous steps
2. **Save IDs** - Store resource IDs in Postman variables ({{category_id}}, {{product_id}}, etc.)
3. **Currency** - All amounts are in Indonesian Rupiah (IDR) without decimals
4. **Date Format** - Use YYYY-MM-DD format for dates
5. **Authentication** - All endpoints except health check and auth require Bearer token
6. **Multi-tenancy** - All data is scoped to the authenticated user's store

## Common Status Codes
- `200 OK` - Success
- `201 Created` - Resource created
- `204 No Content` - Success with no response body
- `400 Bad Request` - Invalid request
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed