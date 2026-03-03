# Hotel Management System

A comprehensive hotel management platform built with Vue.js frontend and PHP backend.

## Features

- **Authentication & Access Control**: Role-based access control with 6 different roles
- **Room Reservations**: Full reservation lifecycle management
- **Housekeeping**: Room status tracking and task management
- **Restaurant & POS**: Menu management with kitchen display system
- **Billing & Payments**: Multiple payment methods and invoice management
- **Inventory Management**: Food, beverage, and supply tracking
- **Staff Management**: Payroll, attendance, and leave management
- **Reporting & Dashboards**: Role-specific dashboards and reports

## Project Structure

```
hotel-management/
├── frontend/           # Vue.js application
├── backend/            # PHP REST API
├── database/           # Database migrations and seeds
└── docs/              # Documentation
```

## Technology Stack

- **Frontend**: Vue.js 3, Tailwind CSS, Pinia
- **Backend**: PHP, RESTful API
- **Database**: MySQL/MariaDB
- **Authentication**: JWT Token-based

## Installation

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan migrate
php artisan serve
```

### Database Setup

```bash
mysql -u root -p
CREATE DATABASE hotel_management;
```

## User Roles

1. **Admin** - Full system access
2. **Manager** - Hotel operations management
3. **Front Desk** - Guest and reservations management
4. **Housekeeping** - Room cleaning and maintenance
5. **Chef** - Kitchen operations
6. **Accountant** - Billing and financial reports

## API Documentation

See `docs/API.md` for complete API endpoints documentation.

## Database Schema

See `database/schema.sql` for database structure.

## License

Proprietary - All rights reserved
