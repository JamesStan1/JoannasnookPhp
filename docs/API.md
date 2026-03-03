## Hotel Management System - API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication
All protected endpoints require Bearer token in Authorization header:
```
Authorization: Bearer {token}
```

### Response Format
Success Response (200):
```json
{
  "success": true,
  "data": {},
  "message": "Success message"
}
```

Error Response:
```json
{
  "success": false,
  "message": "Error message"
}
```

---

## Authentication Endpoints

### Login
**POST** `/auth/login`

Request:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGc...",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@hotel.com",
      "role": "admin"
    }
  }
}
```

### Register
**POST** `/auth/register`

Request:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "+1234567890",
  "role": "guest"
}
```

### Verify Token
**POST** `/auth/verify-token`

Returns current user data if token is valid.

### Logout
**POST** `/auth/logout`

---

## Reservation Endpoints

### Get Available Rooms
**POST** `/reservations/available-rooms`

Request:
```json
{
  "check_in_date": "2024-02-01",
  "check_out_date": "2024-02-05"
}
```

### Create Reservation
**POST** `/reservations`

Request:
```json
{
  "room_id": 1,
  "check_in_date": "2024-02-01",
  "check_out_date": "2024-02-05",
  "number_of_guests": 2,
  "special_requests": "High floor preferred",
  "down_payment": 100
}
```

### Get Reservation
**GET** `/reservations/{id}`

### Get My Reservations
**GET** `/reservations/guest/my-reservations`

### Approve Reservation
**PUT** `/reservations/{id}/approve`

### Check In
**PUT** `/reservations/{id}/check-in`

### Check Out
**PUT** `/reservations/{id}/check-out`

### Cancel Reservation
**PUT** `/reservations/{id}/cancel`

---

## Room Endpoints

### Get All Rooms
**GET** `/rooms`

### Get Room
**GET** `/rooms/{id}`

### Create Room
**POST** `/rooms`

Request:
```json
{
  "room_number": "101",
  "type": "Single",
  "price": 80.00,
  "capacity": 1,
  "description": "Cozy single room"
}
```

### Update Room
**PUT** `/rooms/{id}`

### Update Room Status
**PUT** `/rooms/{id}/status`

Request:
```json
{
  "status": "available"
}
```

Status values: `available`, `occupied`, `dirty`, `maintenance`, `reserved`

---

## Housekeeping Endpoints

### Get My Tasks
**GET** `/housekeeping/my-tasks`

### Get Pending Tasks
**GET** `/housekeeping/pending-tasks`

### Create Task
**POST** `/housekeeping/tasks`

Request:
```json
{
  "room_id": 1,
  "staff_id": null,
  "task_type": "cleaning",
  "description": "Full room cleaning",
  "priority": "medium"
}
```

### Assign Task
**PUT** `/housekeeping/tasks/assign`

Request:
```json
{
  "task_id": 1,
  "staff_id": 4
}
```

### Complete Task
**PUT** `/housekeeping/tasks/{id}/complete`

### Get Room Status
**GET** `/housekeeping/rooms/{id}/status`

---

## Restaurant Endpoints

### Get Menu Items
**GET** `/restaurant/menu`

### Get Items by Category
**GET** `/restaurant/menu/category/{id}`

### Create Menu Item
**POST** `/restaurant/menu-items`

Request:
```json
{
  "name": "Grilled Salmon",
  "category_id": 1,
  "description": "Fresh salmon with herbs",
  "price": 25.00
}
```

### Create Order
**POST** `/restaurant/orders`

Request:
```json
{
  "customer_id": 1,
  "order_type": "restaurant",
  "total_amount": 75.00,
  "items": [
    {
      "menu_item_id": 1,
      "quantity": 2,
      "unit_price": 25.00,
      "special_instructions": "No salt"
    }
  ]
}
```

### Get Kitchen Orders
**GET** `/restaurant/kitchen-orders`

### Update Order Status
**PUT** `/restaurant/orders/{id}/status`

Request:
```json
{
  "status": "completed"
}
```

Status values: `pending`, `in_progress`, `completed`, `cancelled`

---

## Billing Endpoints

### Create Bill
**POST** `/bills`

Request:
```json
{
  "guest_id": 1,
  "reservation_id": 1,
  "bill_type": "room",
  "subtotal": 400,
  "discount": 20,
  "tax": 40,
  "total_amount": 420,
  "items": [
    {
      "description": "Room charge (4 nights)",
      "quantity": 4,
      "unit_price": 100,
      "amount": 400
    }
  ]
}
```

### Get Bill Details
**GET** `/bills/{id}`

### Get Guest Bills
**GET** `/guest/{id}/bills`

### Record Payment
**PUT** `/bills/{id}/payment`

Request:
```json
{
  "amount": 420,
  "method": "card"
}
```

### Generate Receipt
**GET** `/bills/{id}/receipt`

---

## Inventory Endpoints

### Get All Inventory
**GET** `/inventory`

### Get Low Stock Items
**GET** `/inventory/low-stock`

### Add Item
**POST** `/inventory`

Request:
```json
{
  "name": "Salmon",
  "category": "Seafood",
  "quantity": 50,
  "unit_price": 15.00,
  "minimum_stock": 10,
  "supplier": "Fresh Fish Co"
}
```

### Update Stock
**PUT** `/inventory/{id}/stock`

Request:
```json
{
  "quantity": 5,
  "action": "reduce",
  "notes": "Used for dinner service"
}
```

Action values: `add`, `reduce`, `adjust`

### Get Inventory History
**GET** `/inventory/{id}/history`

---

## Staff Endpoints

### Clock In
**POST** `/staff/attendance/clock-in`

### Clock Out
**POST** `/staff/attendance/clock-out`

### Get Today's Record
**GET** `/staff/attendance/today`

### Get Attendance Report
**GET** `/staff/attendance/report?staff_id=1&start_date=2024-02-01&end_date=2024-02-28`

### Request Leave
**POST** `/staff/leaves`

Request:
```json
{
  "start_date": "2024-02-15",
  "end_date": "2024-02-17",
  "leave_type": "casual",
  "reason": "Personal work"
}
```

### Get Pending Leaves
**GET** `/staff/leaves/pending`

### Approve Leave
**PUT** `/staff/leaves/{id}/approve`

### Reject Leave
**PUT** `/staff/leaves/{id}/reject`

Request:
```json
{
  "reason": "Insufficient notice"
}
```

### Get Payroll
**GET** `/staff/{id}/payroll`

### Calculate Payroll
**POST** `/staff/{id}/payroll/calculate`

Request:
```json
{
  "month": "2"
}
```

### Record Payment
**POST** `/staff/{id}/payroll/payment`

Request:
```json
{
  "month": "2024-02",
  "basic_salary": 2000,
  "allowances": 300,
  "deductions": 200,
  "amount": 2100,
  "payment_date": "2024-02-28",
  "payment_method": "bank"
}
```

---

## Admin Endpoints

### Get Dashboard
**GET** `/admin/dashboard`

Returns summary statistics.

### Get Activity Logs
**GET** `/admin/activity-logs?user_id=1&module=reservations&start_date=2024-02-01&end_date=2024-02-28`

### Get Settings
**GET** `/admin/settings`

### Update Settings
**PUT** `/admin/settings`

Request:
```json
{
  "hotel_name": "Luxury Hotel",
  "hotel_phone": "+1-800-LUXURY-1",
  "currency": "USD",
  "tax_rate": "10"
}
```

### Get All Users
**GET** `/admin/users`

### Create User
**POST** `/admin/users`

Request:
```json
{
  "name": "New Staff",
  "email": "staff@hotel.com",
  "password": "password123",
  "phone": "+1234567890",
  "role": "housekeeping"
}
```

### Update User
**PUT** `/admin/users/{id}`

### Delete User
**DELETE** `/admin/users/{id}`

### Export Data
**GET** `/admin/export?type=reservations&format=csv`

Type values: `reservations`, `revenue`, `inventory`
Format values: `csv`, `json`

---

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `405` - Method Not Allowed
- `409` - Conflict
- `500` - Server Error

---

## Rate Limiting

Currently no rate limiting is implemented. For production, implement rate limiting to prevent abuse.

## Pagination

Add to any GET endpoint that returns lists:
- `page=1` - Page number
- `per_page=10` - Items per page

Example: `/reservations?page=1&per_page=20`
