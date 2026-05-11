# Admin Documentation

## Overview

This document describes the complete admin system for the Cambo Express application. The admin system provides role-based access control, user management, product management, order analytics, and more.

## Architecture

### Components

1. **IsAdmin Trait** - Authorization helper methods for checking admin status
2. **IsAdmin Middleware** - Route middleware for protecting admin endpoints
3. **Admin Controllers** - Dedicated controllers for admin operations
4. **Admin Routes** - Protected API routes for admin features
5. **Admin Resources** - Consistent API response formatting

## Authorization System

### Middleware: `IsAdmin`

Located in `app/Http/Middleware/IsAdmin.php`

- Checks if user is authenticated
- Verifies user has 'admin' role
- Returns 403 if user is not admin
- Returns 401 if user is not authenticated

### Trait: `IsAdmin`

Located in `app/Traits/IsAdmin.php`

Available methods:

```php
// Check if authenticated user is admin
isAdmin(): bool

// Throw 403 if user is not admin
authorizeAdmin(): void

// Check if any user is admin
userIsAdmin($user): bool
```

## Admin Routes

All admin routes are prefixed with `/api/v1/admin` and require:
- Authentication (Sanctum token)
- Admin role

### Base Structure

```
POST   /api/v1/auth/login              // Login (any user)
GET    /api/v1/auth/me                 // Get current user
GET    /api/v1/products                // Get all products (public)

// Admin-protected routes
POST   /api/v1/products                // Create product (admin)
PUT    /api/v1/products/{id}           // Update product (admin)
DELETE /api/v1/products/{id}           // Delete product (admin)

POST   /api/v1/categories              // Create category (admin)
PUT    /api/v1/categories/{id}         // Update category (admin)
DELETE /api/v1/categories/{id}         // Delete category (admin)

POST   /api/v1/stocks                  // Create stock log (admin)

GET    /api/v1/roles                   // List roles (admin)
POST   /api/v1/roles                   // Create role (admin)
PUT    /api/v1/roles/{id}              // Update role (admin)
DELETE /api/v1/roles/{id}              // Delete role (admin)

GET    /api/v1/admin/users             // List users (admin)
GET    /api/v1/admin/users/{id}        // Get user details (admin)
PUT    /api/v1/admin/users/{id}        // Update user (admin)
DELETE /api/v1/admin/users/{id}        // Delete user (admin)
PUT    /api/v1/admin/users/{id}/role   // Update user role (admin)
PUT    /api/v1/admin/users/{id}/status // Update user status (admin)
PUT    /api/v1/admin/users/{id}/reset-password // Reset password (admin)

GET    /api/v1/admin/dashboard/overview        // Dashboard overview (admin)
GET    /api/v1/admin/dashboard/sales           // Sales statistics (admin)
GET    /api/v1/admin/dashboard/top-products    // Top selling products (admin)
GET    /api/v1/admin/dashboard/low-stock       // Low stock products (admin)
GET    /api/v1/admin/dashboard/recent-orders   // Recent orders (admin)
GET    /api/v1/admin/dashboard/order-stats     // Order statistics (admin)
GET    /api/v1/admin/dashboard/payment-stats   // Payment statistics (admin)
GET    /api/v1/admin/dashboard/stock-movement  // Stock movement (admin)
```

## API Endpoints Detail

### Product Management

#### Create Product (Admin)
```
POST /api/v1/products
Headers: Authorization: Bearer {token}
Body: {
  "name": "Product Name",
  "category_id": 1,
  "description": "Product Description",
  "price": 99.99,
  "stock": 10,
  "discount": 10,
  "image": <file>
}
```

#### Update Product (Admin)
```
PUT /api/v1/products/{id}
Headers: Authorization: Bearer {token}
Body: {
  "name": "Updated Name",
  "price": 89.99,
  "stock": 15,
  ...
}
```

#### Delete Product (Admin)
```
DELETE /api/v1/products/{id}
Headers: Authorization: Bearer {token}
```

### Category Management

#### Create Category (Admin)
```
POST /api/v1/categories
Headers: Authorization: Bearer {token}
Body: {
  "name": "Category Name",
  "slug": "category-slug"
}
```

#### Update Category (Admin)
```
PUT /api/v1/categories/{id}
Headers: Authorization: Bearer {token}
Body: {
  "name": "Updated Name",
  "slug": "updated-slug",
  "status": "enable"
}
```

#### Delete Category (Admin)
```
DELETE /api/v1/categories/{id}
Headers: Authorization: Bearer {token}
```

### Stock Management

#### Create Stock Log (Admin)
```
POST /api/v1/stocks
Headers: Authorization: Bearer {token}
Body: {
  "product_id": 1,
  "quantity": 10,
  "type": "in",  // 'in' or 'out'
  "note": "Optional note"
}
```

### User Management

#### List Users (Admin)
```
GET /api/v1/admin/users?search=john&role=customer&status=enable&per_page=15
Headers: Authorization: Bearer {token}
```

Query Parameters:
- `search` - Search by name, email, or phone
- `role` - Filter by role (admin, customer)
- `status` - Filter by status (enable, disable)
- `per_page` - Items per page (default: 15)

#### Get User Details (Admin)
```
GET /api/v1/admin/users/{id}
Headers: Authorization: Bearer {token}
```

#### Update User (Admin)
```
PUT /api/v1/admin/users/{id}
Headers: Authorization: Bearer {token}
Body: {
  "name": "New Name",
  "email": "newemail@example.com",
  "phone": "0123456789",
  "role_id": 1,
  "status": "enable",
  "address": "123 Street",
  "profile": "User profile"
}
```

#### Update User Role (Admin)
```
PUT /api/v1/admin/users/{id}/role
Headers: Authorization: Bearer {token}
Body: {
  "role_id": 2
}
```

#### Update User Status (Admin)
```
PUT /api/v1/admin/users/{id}/status
Headers: Authorization: Bearer {token}
Body: {
  "status": "disable"  // 'enable' or 'disable'
}
```

#### Reset User Password (Admin)
```
PUT /api/v1/admin/users/{id}/reset-password
Headers: Authorization: Bearer {token}
Body: {
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

#### Delete User (Admin)
```
DELETE /api/v1/admin/users/{id}
Headers: Authorization: Bearer {token}
```

Note: Admin cannot delete their own account.

### Dashboard & Analytics

#### Dashboard Overview (Admin)
```
GET /api/v1/admin/dashboard/overview
Headers: Authorization: Bearer {token}

Response: {
  "data": {
    "users": { "total": 100, "customers": 90, "admins": 10 },
    "orders": { "total": 50, "active": 5, "total_revenue": 5000 },
    "products": { "total": 200, "low_stock": 5 },
    "payments": { "pending": 2 }
  }
}
```

#### Sales Statistics (Admin)
```
GET /api/v1/admin/dashboard/sales?period=30
Headers: Authorization: Bearer {token}

Query Parameters:
- period: Days to look back (default: 30)

Response: {
  "data": {
    "period_days": "30",
    "total_sales": 5000,
    "total_orders": 50,
    "average_order_value": 100,
    "sales_by_date": [
      { "date": "2026-02-17", "orders": 5, "revenue": 500 },
      ...
    ]
  }
}
```

#### Top Selling Products (Admin)
```
GET /api/v1/admin/dashboard/top-products?limit=10&period=30
Headers: Authorization: Bearer {token}

Query Parameters:
- limit: Number of products (default: 10)
- period: Days to look back (default: 30)

Response: {
  "data": {
    "period_days": "30",
    "top_products": [
      {
        "id": 1,
        "name": "Product Name",
        "category": "Category Name",
        "quantity_sold": 100,
        "orders_count": 20,
        "price": 99.99
      },
      ...
    ]
  }
}
```

#### Low Stock Products (Admin)
```
GET /api/v1/admin/dashboard/low-stock?threshold=10&per_page=15
Headers: Authorization: Bearer {token}

Query Parameters:
- threshold: Stock threshold (default: 10)
- per_page: Items per page (default: 15)
```

#### Recent Orders (Admin)
```
GET /api/v1/admin/dashboard/recent-orders?limit=10&status=pending
Headers: Authorization: Bearer {token}

Query Parameters:
- limit: Number of orders (default: 10)
- status: Filter by status (pending, processing, completed, cancelled)
```

#### Order Statistics (Admin)
```
GET /api/v1/admin/dashboard/order-stats
Headers: Authorization: Bearer {token}

Response: {
  "data": {
    "pending": { "status": "pending", "count": 10, "total_revenue": 1000 },
    "completed": { "status": "completed", "count": 50, "total_revenue": 5000 },
    ...
  }
}
```

#### Payment Statistics (Admin)
```
GET /api/v1/admin/dashboard/payment-stats
Headers: Authorization: Bearer {token}

Response: {
  "data": {
    "by_status": {
      "success": { "status": "success", "count": 40, "total_amount": 4000 },
      "pending": { "status": "pending", "count": 5, "total_amount": 500 },
      ...
    },
    "total_processed": 4000
  }
}
```

#### Stock Movement (Admin)
```
GET /api/v1/admin/dashboard/stock-movement?days=30
Headers: Authorization: Bearer {token}

Query Parameters:
- days: Days to look back (default: 30)
```

## Usage Examples

### Login as Admin
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### Create a Product (with admin token)
```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop",
    "category_id": 1,
    "description": "High-end laptop",
    "price": 1299.99,
    "stock": 5,
    "discount": 10
  }'
```

### Get Dashboard Overview (with admin token)
```bash
curl -X GET http://localhost:8000/api/v1/admin/dashboard/overview \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

### List All Users (with admin token)
```bash
curl -X GET http://localhost:8000/api/v1/admin/users?search=john&per_page=10 \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

## Security Notes

1. **All admin endpoints require authentication** - Must provide valid Sanctum token
2. **All admin endpoints require admin role** - User must have 'admin' role
3. **Role-based access control** - Implemented via middleware
4. **Protected operations** - Admin cannot delete their own account
5. **Password reset** - Requires confirmation for security

## Error Responses

### Unauthorized (401)
```json
{
  "message": "Unauthorized. Authentication required."
}
```

### Forbidden (403)
```json
{
  "message": "Unauthorized. Admin access required."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found"
}
```

### Validation Error (422)
```json
{
  "message": "Validation error",
  "errors": {
    "field": ["error message"]
  }
}
```

## Implementation Notes

- All admin endpoints follow REST conventions
- Responses use consistent JSON format
- Pagination support for list endpoints (default: 15 per page)
- Filtering and searching available on user endpoints
- Statistics and analytics computed on-the-fly
- Stock management tracks all movements with notes
- Orders and payments statistics grouped by status

## Future Enhancements

- Audit logging for all admin actions
- Batch operations for users and products
- Advanced filtering and export capabilities
- Admin activity logs and reports
- Two-factor authentication for admins
- Role-based permission system (fine-grained)
- Admin approval workflows
