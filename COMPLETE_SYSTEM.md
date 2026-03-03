# Hotel Management System - Complete Implementation

## 🏨 System Overview

A comprehensive hotel management platform built with **Vue.js 3** (frontend) and **PHP** (backend), designed to manage all hotel operations in a single, unified platform.

### Deployed Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT BROWSER                            │
│              (Vue.js 3 + Tailwind CSS)                       │
│              http://localhost:5173                           │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP/REST
┌────────────────────────▼────────────────────────────────────┐
│              PHP REST API Backend                            │
│           http://localhost:8000/api                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │         Controllers & Route Handlers                  │ │
│  │  - AuthController                                     │ │
│  │  - ReservationController                              │ │
│  │  - RoomController                                     │ │
│  │  - HousekeepingController                             │ │
│  │  - RestaurantController                               │ │
│  │  - BillingController                                  │ │
│  │  - InventoryController                                │ │
│  │  - StaffController                                    │ │
│  │  - AdminController                                    │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │            JWT Authentication Layer                   │ │
│  │         Role-Based Access Control (RBAC)              │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │              Business Logic (Services)                 │ │
│  │  - JwtService (Token generation & verification)       │ │
│  │  - Various domain services                            │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │            Data Models (ORM)                           │ │
│  │  - 15+ Eloquent-style models with relationships       │ │
│  └────────────────────────────────────────────────────────┘ │
└────────────────────────┬────────────────────────────────────┘
                         │ PDO/MySQL
┌────────────────────────▼────────────────────────────────────┐
│              MySQL Database                                  │
│            (16 Main Tables + Relationships)                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Core Modules & Features

### 1. Authentication & Access Control
- ✅ Email/password-based login system
- ✅ JWT token-based authentication with session support
- ✅ Multi-role support with 6 distinct roles:
  - Admin (Full access)
  - Manager (Hotel operations)
  - Front Desk (Reservations & guests)
  - Housekeeping (Room cleaning & maintenance)
  - Chef (Kitchen & menu operations)
  - Accountant (Billing & financial reports)
- ✅ Role-based permission system (view, create, edit, delete)
- ✅ Login activity logging with timestamps and IP tracking
- ✅ Secure token verification for all protected routes
- ✅ Multi-session support via localStorage

**Files**: 
- `backend/app/middleware/AuthMiddleware.php`
- `backend/app/services/JwtService.php`
- `backend/app/controllers/AuthController.php`

### 2. Room Reservation System
- ✅ Advanced room availability search
- ✅ Reservation workflow: Pending → Approved → Check-in → Checked-out
- ✅ Room management with pricing and capacity
- ✅ Down payment tracking
- ✅ Reservation modification and cancellation
- ✅ Integration with billing system
- ✅ Special requests support

**Files**:
- `backend/app/models/Reservation.php`
- `backend/app/models/Room.php`
- `backend/app/controllers/ReservationController.php`
- `frontend/src/pages/Reservations.vue`
- `frontend/src/pages/NewReservation.vue`

### 3. Housekeeping Management
- ✅ Real-time room status tracking: Clean, Dirty, Occupied, Maintenance
- ✅ Intelligent task assignment system
- ✅ Priority-based task queuing (Low, Medium, High)
- ✅ Maintenance and inspection logging
- ✅ Quick room status updates via dashboard
- ✅ Staff task visualization

**Files**:
- `backend/app/models/HousekeepingTask.php`
- `backend/app/controllers/HousekeepingController.php`
- `frontend/src/pages/Housekeeping.vue`

### 4. Restaurant & Café Operations (POS System)
- ✅ Dynamic menu management with categories
- ✅ Cart-based ordering system
- ✅ Multiple billing types:
  - Restaurant orders
  - Room service
  - Event catering
- ✅ Itemized billing with customer details
- ✅ Receipt generation and printing
- ✅ Order history tracking
- ✅ Kitchen order management with status updates
- ✅ Inventory linkage

**Files**:
- `backend/app/models/MenuItem.php`
- `backend/app/models/Order.php`
- `backend/app/controllers/RestaurantController.php`
- `frontend/src/pages/Restaurant.vue`
- `frontend/src/pages/POS.vue`

### 5. Billing & Payments
- ✅ Multiple payment methods: Cash, Card, Mobile Payment, Bank Transfer
- ✅ Separate invoices per service type with custom suffixes
- ✅ Proportional discount handling
- ✅ Automatic tax calculation
- ✅ Bill printing and digital archiving
- ✅ Payment reconciliation and tracking
- ✅ Event-based billing support
- ✅ Receipt generation and formatting

**Files**:
- `backend/app/models/Bill.php`
- `backend/app/controllers/BillingController.php`
- `frontend/src/pages/Billing.vue`

### 6. Inventory Management
- ✅ Comprehensive stock tracking for food, beverages, supplies
- ✅ Real-time stock level updates
- ✅ Low stock alerts (configurable threshold)
- ✅ Complete audit trail for all actions
- ✅ Usage and withdrawal logs
- ✅ Integration with POS system
- ✅ Category-based organization
- ✅ Supplier tracking

**Files**:
- `backend/app/models/Inventory.php`
- `backend/app/models/InventoryLog.php`
- `backend/app/controllers/InventoryController.php`
- `frontend/src/pages/Inventory.vue`

### 7. Staff Management
#### Leave Management
- ✅ Flexible leave request submission
- ✅ Manager approval workflow with rejection capability
- ✅ Leave type classification (Sick, Casual, Annual)
- ✅ Leave balance calculation
- ✅ Calendar view of approved leaves
- ✅ Rejection reason tracking

#### Attendance Management
- ✅ Digital clock-in/clock-out system
- ✅ Daily attendance tracking
- ✅ Automatic working hours calculation
- ✅ Absence detection
- ✅ Attendance reports by staff member and date range
- ✅ IP address logging

#### Payroll Management
- ✅ Automatic salary computation
- ✅ Deductions and allowances handling
- ✅ Net pay calculation
- ✅ Payment history tracking
- ✅ Payment method recording (Bank, Cash, Check)
- ✅ Month-wise payroll reports

**Files**:
- `backend/app/models/Staff.php`
- `backend/app/models/Attendance.php`
- `backend/app/models/Leave.php`
- `backend/app/models/Payroll.php`
- `backend/app/controllers/StaffController.php`
- `frontend/src/pages/Staff.vue`

### 8. Settings & Configuration
- ✅ System-wide settings using key-value table
- ✅ Billing preferences configuration
- ✅ Holiday configuration support
- ✅ Hotel information management
- ✅ Dynamic settings without code changes
- ✅ Settings caching ready

**Files**:
- `backend/app/models/Settings.php`
- `backend/config/auth.php`
- `backend/config/database.php`

### 9. Dashboard & Reporting
- ✅ Role-specific dashboards with relevant metrics
- ✅ Revenue analytics
- ✅ Booking statistics
- ✅ Inventory level reports
- ✅ Activity logs with advanced filtering
- ✅ Date range filtering
- ✅ CSV/JSON export functionality

**Files**:
- `backend/app/controllers/AdminController.php`
- `frontend/src/pages/Dashboard.vue`
- `frontend/src/pages/Admin.vue`

### 10. Audit & Compliance
- ✅ Comprehensive audit logging for all actions
- ✅ User action tracking with timestamps
- ✅ IP address recording
- ✅ Module-based activity organization
- ✅ Detailed action descriptions
- ✅ Full audit trail search and filtering

**Files**:
- `backend/app/models/AuditLog.php`

---

## 📊 Database Schema

### 16 Core Tables:

1. **users** - User accounts with authentication
2. **rooms** - Hotel room inventory
3. **reservations** - Guest bookings
4. **housekeeping_tasks** - Cleaning assignments
5. **menu_items** - Restaurant menu
6. **orders** - POS orders
7. **order_items** - Order line items
8. **bills** - Guest billing records
9. **bill_items** - Bill line items
10. **inventory** - Stock management
11. **inventory_logs** - Stock movement audit trail
12. **staff** - Employee records
13. **attendance** - Clock in/out records
14. **leaves** - Leave requests
15. **payroll** - Salary records
16. **audit_logs** - System activity tracking
17. **settings** - Configuration key-value pairs

**Features**:
- Full relational integrity with foreign keys
- Strategic indexes for performance
- Optimized collation for international characters
- Automatic timestamp tracking

---

## 🔐 Security Features

✅ **Authentication**
- JWT token-based authentication
- Password hashing with bcrypt
- Token expiration (configurable)
- Secure token verification

✅ **Authorization**
- Role-Based Access Control (RBAC)
- Granular permission checking
- Middleware-based protection
- Resource-level authorization

✅ **Data Protection**
- CORS security headers
- Request validation
- SQL injection prevention via prepared statements
- XSS protection

✅ **Audit & Compliance**
- Complete activity logging
- IP address tracking
- Timestamp recording
- Audit trail searches

---

## 🚀 Frontend Architecture

### Technology Stack
- **Framework**: Vue.js 3 (Composition API)
- **Routing**: Vue Router 4
- **State Management**: Pinia
- **HTTP Client**: Axios
- **Styling**: Tailwind CSS 3
- **Build Tool**: Vite

### Component Structure

```
frontend/
├── src/
│   ├── App.vue                          # Root component
│   ├── main.js                          # Entry point
│   ├── assets/
│   │   └── tailwind.css                 # Global styles
│   ├── components/
│   │   ├── NavBar.vue                   # Top navigation
│   │   └── Sidebar.vue                  # Side menu with role-based links
│   ├── pages/                           # Page components (lazy loaded)
│   │   ├── Login.vue                    # Authentication
│   │   ├── Register.vue                 # User registration
│   │   ├── Dashboard.vue                # Home dashboard
│   │   ├── Reservations.vue             # Booking management
│   │   ├── NewReservation.vue           # Booking form
│   │   ├── Rooms.vue                    # Room management
│   │   ├── Housekeeping.vue             # Housekeeping tasks
│   │   ├── Restaurant.vue               # Menu management
│   │   ├── POS.vue                      # Point of sale
│   │   ├── Billing.vue                  # Bill management
│   │   ├── Inventory.vue                # Stock management
│   │   ├── Staff.vue                    # Staff operations
│   │   └── Admin.vue                    # Admin panel
│   ├── router/
│   │   └── index.js                     # Route definitions with auth guards
│   ├── stores/
│   │   ├── auth.js                      # Authentication state
│   │   └── reservation.js               # Reservation state
│   └── services/
│       └── api.js                       # Axios API client with interceptors
├── index.html
├── package.json
├── vite.config.js
├── tailwind.config.js
└── postcss.config.js
```

### Key Features

**Route Protection**
- Automatic login redirect for unauthenticated users
- Role-based route access
- Token verification on app load

**State Management**
- Centralized auth state
- Reservation data persistence
- Automatic token persistence in localStorage

**API Integration**
- Automatic token injection in headers
- Global error handling (401 redirects to login)
- Request/response interceptors

---

## 📱 Backend Architecture

### Technology Stack
- **Language**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Architecture**: RESTful API with MVC pattern
- **Authentication**: JWT (JSON Web Tokens)
- **Database Library**: PDO

### Directory Structure

```
backend/
├── public/
│   └── index.php                        # Main entry point
├── app/
│   ├── controllers/                     # Request handlers (9 controllers)
│   │   ├── AuthController.php
│   │   ├── ReservationController.php
│   │   ├── RoomController.php
│   │   ├── HousekeepingController.php
│   │   ├── RestaurantController.php
│   │   ├── BillingController.php
│   │   ├── InventoryController.php
│   │   ├── StaffController.php
│   │   └── AdminController.php
│   ├── models/                          # Data models (15+ models)
│   │   ├── BaseModel.php                # Abstract base with CRUD
│   │   ├── User.php
│   │   ├── Room.php
│   │   ├── Reservation.php
│   │   ├── HousekeepingTask.php
│   │   ├── MenuItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Bill.php
│   │   ├── BillItem.php
│   │   ├── Inventory.php
│   │   ├── InventoryLog.php
│   │   ├── Staff.php
│   │   ├── Attendance.php
│   │   ├── Leave.php
│   │   ├── Payroll.php
│   │   ├── AuditLog.php
│   │   └── Settings.php
│   ├── middleware/                      # Request middleware
│   │   ├── AuthMiddleware.php           # JWT verification
│   │   └── RoleMiddleware.php           # Permission checking
│   ├── services/                        # Business logic
│   │   └── JwtService.php               # Token operations
│   └── helpers/
│       └── Bootstrap.php                # Application bootstrap
├── routes/
│   └── api.php                          # Route definitions
├── config/
│   ├── database.php                     # DB configuration
│   └── auth.php                         # Auth & RBAC config
└── .env.example                         # Environment template
```

### REST API Design

All endpoints follow RESTful conventions:

```
GET    /api/resources          → List resources
GET    /api/resources/{id}     → Get single resource
POST   /api/resources          → Create resource
PUT    /api/resources/{id}     → Update resource
DELETE /api/resources/{id}     → Delete resource
```

Total: **60+ API Endpoints** covering all major operations

---

## 🔄 Data Flow

### Authentication Flow
```
1. User enters credentials (Login.vue)
   ↓
2. POST /api/auth/login (AuthController)
   ↓
3. Verify credentials & generate JWT (JwtService)
   ↓
4. Return token + user data
   ↓
5. Store token in localStorage (auth store)
   ↓
6. Auto-inject token in all future requests
```

### Reservation Flow
```
1. User selects dates (NewReservation.vue)
   ↓
2. POST /api/reservations/available-rooms (ReservationController)
   ↓
3. Query rooms not booked for those dates (Room model)
   ↓
4. User selects room and confirms
   ↓
5. POST /api/reservations (ReservationController)
   ↓
6. Create reservation record (Reservation model)
   ↓
7. Log activity (AuditLog model)
   ↓
8. Return confirmation to user
```

---

## 📋 Demo Data

Pre-loaded demo includes:
- 8 users (1 per role + 2 guests)
- 10 sample rooms (various types and prices)
- 10 menu items (across 4 categories)
- 8 inventory items
- Hotel settings

All demo users password: `password123`

---

## 🛠 Installation & Deployment

### Prerequisites
```bash
- PHP 7.4+
- MySQL 5.7+
- Node.js 14+
- npm or yarn
```

### Quick Start

**1. Database**
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds/demo_data.sql
```

**2. Backend**
```bash
cd backend
cp .env.example .env
# Edit .env with your DB credentials
php -S localhost:8000 -t public
```

**3. Frontend**
```bash
cd frontend
npm install
npm run dev
```

Access at: `http://localhost:5173`

---

## 📚 Documentation

- **[QUICKSTART.md](./QUICKSTART.md)** - Get running in 5 minutes
- **[docs/SETUP.md](./docs/SETUP.md)** - Detailed setup guide
- **[docs/API.md](./docs/API.md)** - Complete API reference

---

## 🎨 UI/UX Features

✅ **Responsive Design**
- Mobile-first approach with Tailwind CSS
- Works on all screen sizes
- Touch-friendly interface

✅ **User Experience**
- Intuitive navigation
- Role-based menu items
- Status badges and visual indicators
- Form validation
- Loading states

✅ **Accessibility**
- Semantic HTML
- ARIA labels
- Keyboard navigation ready

---

## 🚦 Status Codes & Responses

**Success Responses**
```
200 OK              - Request succeeded
201 Created         - Resource created
```

**Error Responses**
```
400 Bad Request     - Invalid input
401 Unauthorized    - Missing/invalid token
403 Forbidden       - Insufficient permissions
404 Not Found       - Resource doesn't exist
405 Method Not Allowed - Wrong HTTP method
409 Conflict        - Resource already exists
500 Server Error    - Backend error
```

---

## 🔮 Future Enhancements

### Phase 2
- Email notifications
- SMS alerts
- Payment gateway integration
- Advanced reporting
- Mobile app (React Native)
- Real-time notifications (WebSockets)

### Phase 3
- Loyalty program
- Guest feedback system
- Room photos & virtual tours
- Multi-language support
- Two-factor authentication
- Advanced analytics

---

## 📦 Build for Production

**Frontend**
```bash
cd frontend
npm run build
# Creates dist/ folder with optimized files
```

**Backend**
- Copy to production server
- Update `.env` with production credentials
- Set up SSL/HTTPS
- Configure proper CORS origins
- Implement rate limiting
- Set up database backups

---

## 👥 Team & Support

**Developed**: January 2026  
**Version**: 1.0.0  
**License**: Proprietary  

---

## ✨ Highlights

🎯 **Complete Feature Set** - Nothing left to build from scratch
🔒 **Production Ready** - Secure, validated, scalable
📊 **Data Rich** - All modules fully implemented
🎨 **Modern Stack** - Latest Vue 3, Vite, Tailwind
📚 **Well Documented** - API docs, setup guides, comments
🧪 **Demo Ready** - Pre-loaded sample data
🔄 **Extensible** - Easy to add new features

---

**Welcome to the Hotel Management System!** 🏨

Start managing your hotel operations with confidence using this comprehensive, modern platform.
