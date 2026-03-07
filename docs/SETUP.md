# Hotel Management System - Setup Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Node.js 14+ and npm
- Composer

## Backend Setup

### 1. Copy Environment Configuration

```bash
cd backend
cp .env.example .env
```

Edit `.env` and configure your database:
```
DB_HOST=127.0.0.1
DB_DATABASE=hotel_management
DB_USERNAME=root
DB_PASSWORD=your_password
JWT_SECRET=your-secret-key-change-in-production
```

### 2. Create Database

```bash
mysql -u root -p
CREATE DATABASE hotel_management;
```

### 3. Import Database Schema

```bash
mysql -u root -p hotel_management < ../database/schema.sql
mysql -u root -p hotel_management < ../database/seeds/demo_data.sql
```

### 4. Install PHP Dependencies

```bash
composer install
```

Note: You'll need to create a `composer.json` file or use PHP's built-in server without Composer for this simple setup.

### 5. Start PHP Development Server

```bash
php -S localhost:8000 -t public
```

The API will be available at `http://localhost:8000/api`

## Frontend Setup

### 1. Install Dependencies

```bash
cd frontend
npm install
```

### 2. Start Development Server

```bash
npm run dev
```

The frontend will be available at `http://localhost:5173`

---

## Deploying to Hostinger (Shared PHP Hosting)

Hostinger's auto-deploy tool shows **"Unsupported framework or invalid project structure"** because the repo root contains neither a PHP entry point nor a `package.json`. Follow the steps below for a correct manual deployment.

### Target file layout on Hostinger

```
public_html/              ← web root (Hostinger)
├── .htaccess             ← SPA routing (copied from frontend/dist/)
├── index.html            ← Vue build entry (from frontend/dist/)
├── assets/               ← Vue build assets (from frontend/dist/)
└── api/                  ← PHP backend
    ├── .htaccess         ← PHP URL routing (from backend/public/)
    ├── index.php         ← PHP entry point (from backend/public/)
    ├── app/              ← PHP app code (from backend/app/)
    ├── config/           ← PHP config (from backend/config/)
    ├── routes/           ← PHP routes (from backend/routes/)
    ├── uploads/          ← File uploads (from backend/public/uploads/)
    └── .env              ← Backend secrets (create manually – do NOT commit)
```

### Step 1 – Build the Vue.js frontend

```bash
cd frontend
npm install
npm run build
```

This creates `frontend/dist/` containing `index.html`, `assets/`, and `.htaccess`.

### Step 2 – Upload files via Hostinger File Manager or FTP

| Local path | Upload to `public_html/` |
|---|---|
| `frontend/dist/` (all contents) | `public_html/` (root) |
| `backend/public/index.php` | `public_html/api/index.php` |
| `backend/public/.htaccess` | `public_html/api/.htaccess` |
| `backend/public/uploads/` | `public_html/api/uploads/` |
| `backend/app/` | `public_html/api/app/` |
| `backend/config/` | `public_html/api/config/` |
| `backend/routes/` | `public_html/api/routes/` |

> **Do NOT upload** `backend/.env` – create it fresh on the server (see Step 3).

### Step 3 – Create the backend `.env` on Hostinger

Create `public_html/api/.env` via File Manager with your production values:

```
APP_NAME="Hotel Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_hostinger_db_name
DB_USERNAME=your_hostinger_db_user
DB_PASSWORD=your_hostinger_db_password

JWT_SECRET=replace-with-a-long-random-string
JWT_ALGORITHM=HS256
JWT_EXPIRATION=3600

FRONTEND_URL=https://yourdomain.com
```

> **Important:** Set `FRONTEND_URL` to your actual domain. This is used for CORS.  
> Set `APP_DEBUG=false` in production to avoid leaking error details.

### Step 4 – Import the database

In Hostinger hPanel → **Databases → phpMyAdmin**, import:

1. `database/schema.sql`
2. `database/seeds/demo_data.sql` *(optional – demo data only)*

### Step 5 – Verify the deployment

- Open `https://yourdomain.com` → Vue SPA should load.
- Open `https://yourdomain.com/api/auth/login` in a REST client → should return JSON.

### Fixing "Unsupported framework" in Hostinger's Git auto-deploy

The root `composer.json` (already added to this repo) signals Hostinger that this is a PHP project. However, because Hostinger's PHP auto-deploy does not run `npm build`, **use manual upload (Steps 1–2 above) instead of Git auto-deploy** for this full-stack project.

---

## Demo Login Credentials

All demo users have password: `password123`

### Admin User
- Email: `admin@hotel.com`
- Role: Admin

### Manager
- Email: `manager@hotel.com`
- Role: Manager

### Front Desk
- Email: `frontdesk@hotel.com`
- Role: Front Desk

### Housekeeping
- Email: `housekeeping@hotel.com`
- Role: Housekeeping

### Chef
- Email: `chef@hotel.com`
- Role: Chef

### Accountant
- Email: `accountant@hotel.com`
- Role: Accountant

### Guest User
- Email: `john@email.com`
- Email: `jane@email.com`

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login user
- `POST /api/auth/register` - Register new user
- `POST /api/auth/verify-token` - Verify token
- `POST /api/auth/logout` - Logout user

### Reservations
- `POST /api/reservations/available-rooms` - Get available rooms
- `POST /api/reservations` - Create reservation
- `GET /api/reservations/{id}` - Get reservation details
- `GET /api/reservations/guest/my-reservations` - Get guest's reservations
- `PUT /api/reservations/{id}/approve` - Approve reservation
- `PUT /api/reservations/{id}/check-in` - Check-in guest
- `PUT /api/reservations/{id}/check-out` - Check-out guest
- `PUT /api/reservations/{id}/cancel` - Cancel reservation

### Rooms
- `GET /api/rooms` - Get all rooms
- `GET /api/rooms/{id}` - Get room details
- `POST /api/rooms` - Create room
- `PUT /api/rooms/{id}` - Update room
- `PUT /api/rooms/{id}/status` - Update room status

### Housekeeping
- `GET /api/housekeeping/my-tasks` - Get staff's tasks
- `GET /api/housekeeping/pending-tasks` - Get pending tasks
- `POST /api/housekeeping/tasks` - Create task
- `PUT /api/housekeeping/tasks/assign` - Assign task
- `PUT /api/housekeeping/tasks/{id}/complete` - Complete task

### Restaurant
- `GET /api/restaurant/menu` - Get menu items
- `GET /api/restaurant/menu/category/{id}` - Get items by category
- `POST /api/restaurant/menu-items` - Create menu item
- `POST /api/restaurant/orders` - Create order
- `GET /api/restaurant/kitchen-orders` - Get kitchen orders
- `PUT /api/restaurant/orders/{id}/status` - Update order status

### Billing
- `POST /api/bills` - Create bill
- `GET /api/bills/{id}` - Get bill details
- `GET /api/guest/{id}/bills` - Get guest bills
- `PUT /api/bills/{id}/payment` - Record payment
- `GET /api/bills/{id}/receipt` - Generate receipt

### Inventory
- `GET /api/inventory` - Get all inventory
- `GET /api/inventory/low-stock` - Get low stock items
- `POST /api/inventory` - Add inventory item
- `PUT /api/inventory/{id}/stock` - Update stock
- `GET /api/inventory/{id}/history` - Get inventory history

### Staff
- `POST /api/staff/attendance/clock-in` - Clock in
- `POST /api/staff/attendance/clock-out` - Clock out
- `GET /api/staff/attendance/today` - Get today's record
- `GET /api/staff/attendance/report` - Get attendance report
- `POST /api/staff/leaves` - Request leave
- `GET /api/staff/leaves/pending` - Get pending leaves
- `PUT /api/staff/leaves/{id}/approve` - Approve leave
- `PUT /api/staff/leaves/{id}/reject` - Reject leave
- `GET /api/staff/{id}/payroll` - Get payroll
- `POST /api/staff/{id}/payroll/calculate` - Calculate payroll
- `POST /api/staff/{id}/payroll/payment` - Record payment

### Admin
- `GET /api/admin/dashboard` - Get dashboard stats
- `GET /api/admin/activity-logs` - Get activity logs
- `GET /api/admin/settings` - Get settings
- `PUT /api/admin/settings` - Update settings
- `GET /api/admin/users` - Get all users
- `POST /api/admin/users` - Create user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `GET /api/admin/export` - Export data

## Project Structure

```
hotel-management/
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── NavBar.vue
│   │   │   └── Sidebar.vue
│   │   ├── pages/
│   │   │   ├── Login.vue
│   │   │   ├── Dashboard.vue
│   │   │   ├── Reservations.vue
│   │   │   ├── Rooms.vue
│   │   │   ├── Housekeeping.vue
│   │   │   ├── Restaurant.vue
│   │   │   ├── POS.vue
│   │   │   ├── Billing.vue
│   │   │   ├── Inventory.vue
│   │   │   ├── Staff.vue
│   │   │   └── Admin.vue
│   │   ├── stores/
│   │   │   ├── auth.js
│   │   │   └── reservation.js
│   │   ├── services/
│   │   │   └── api.js
│   │   ├── router/
│   │   │   └── index.js
│   │   ├── App.vue
│   │   └── main.js
│   ├── index.html
│   ├── package.json
│   ├── vite.config.js
│   └── tailwind.config.js
│
├── backend/
│   ├── public/
│   │   └── index.php
│   ├── app/
│   │   ├── controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ReservationController.php
│   │   │   ├── RoomController.php
│   │   │   ├── HousekeepingController.php
│   │   │   ├── RestaurantController.php
│   │   │   ├── BillingController.php
│   │   │   ├── InventoryController.php
│   │   │   ├── StaffController.php
│   │   │   └── AdminController.php
│   │   ├── models/
│   │   │   ├── BaseModel.php
│   │   │   ├── User.php
│   │   │   ├── Room.php
│   │   │   ├── Reservation.php
│   │   │   ├── HousekeepingTask.php
│   │   │   ├── MenuItem.php
│   │   │   ├── Order.php
│   │   │   ├── Bill.php
│   │   │   ├── Inventory.php
│   │   │   ├── InventoryLog.php
│   │   │   ├── Staff.php
│   │   │   ├── Attendance.php
│   │   │   ├── Leave.php
│   │   │   ├── Payroll.php
│   │   │   ├── AuditLog.php
│   │   │   └── Settings.php
│   │   ├── middleware/
│   │   │   ├── AuthMiddleware.php
│   │   │   └── RoleMiddleware.php
│   │   ├── services/
│   │   │   └── JwtService.php
│   │   └── helpers/
│   │       └── Bootstrap.php
│   ├── routes/
│   │   └── api.php
│   ├── config/
│   │   ├── database.php
│   │   └── auth.php
│   └── .env.example
│
├── database/
│   ├── migrations/
│   ├── seeds/
│   │   └── demo_data.sql
│   └── schema.sql
│
└── README.md
```

## Key Features Implemented

✅ Authentication & Access Control
- Email/password login
- JWT token-based authentication
- Role-Based Access Control (6 roles)
- Login activity logging

✅ Room Reservations
- Search available rooms
- Create/modify/cancel reservations
- Reservation lifecycle management
- Down payment tracking

✅ Housekeeping Management
- Task assignment and tracking
- Room status management
- Priority-based task queue

✅ Restaurant & POS
- Menu management
- POS system with cart
- Order management
- Kitchen display

✅ Billing & Payments
- Multiple bill types
- Payment tracking
- Receipt generation
- Payment reconciliation

✅ Inventory Management
- Stock tracking
- Low stock alerts
- Audit trail
- Category organization

✅ Staff Management
- Attendance tracking (clock in/out)
- Leave management
- Payroll system
- Staff directory

✅ Admin & Settings
- Activity logging
- System settings
- User management
- Data export

## Security Features

- JWT authentication with token verification
- Role-based access control
- Password hashing (bcrypt)
- Activity audit logs
- CORS protection
- Input validation

## Performance Optimizations

- Database indexes on frequently queried columns
- Pagination support for large datasets
- Efficient query patterns
- Caching-ready architecture

## Future Enhancements

- Email notifications
- SMS alerts
- Real-time updates with WebSockets
- Payment gateway integration
- Mobile app
- Advanced reporting and analytics
- Multi-language support
- Two-factor authentication
- Room photos and virtual tours
- Guest feedback system

## Support

For issues or questions, please refer to the API documentation or contact the development team.
