# Quick Start Guide

## System Requirements

- **PHP**: 7.4+
- **MySQL**: 5.7+
- **Node.js**: 14+ with npm
- **Modern Browser**: Chrome, Firefox, Safari, Edge

## Installation Steps

### Step 1: Database Setup

Open MySQL and run:

```sql
CREATE DATABASE hotel_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_management;
```

Import the schema and sample data:

```bash
mysql -u root -p hotel_management < database/schema.sql
mysql -u root -p hotel_management < database/seeds/demo_data.sql
```

### Step 2: Backend Configuration

Navigate to backend folder:

```bash
cd backend
cp .env.example .env
```

Edit `.env` file with your database credentials:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_management
DB_USERNAME=root
DB_PASSWORD=your_password
JWT_SECRET=your-secret-key-12345
```

### Step 3: Start Backend Server

From backend directory:

```bash
php -S localhost:8000 -t public
```

Backend running at: `http://localhost:8000`

### Step 4: Frontend Setup

Open new terminal, navigate to frontend:

```bash
cd frontend
npm install
npm run dev
```

Frontend running at: `http://localhost:5173`

## Login with Demo Accounts

The demo data includes several pre-configured users. All passwords are: `password123`

1. **Admin**: admin@hotel.com
   - Full system access
   - Dashboard, settings, user management

2. **Manager**: manager@hotel.com
   - Hotel operations management
   - Room, reservations, housekeeping

3. **Front Desk**: frontdesk@hotel.com
   - Guest and reservation management
   - Check-in/check-out

4. **Housekeeping**: housekeeping@hotel.com
   - Task management and assignment
   - Room status updates

5. **Chef**: chef@hotel.com
   - Kitchen operations
   - Order management
   - Menu items

6. **Accountant**: accountant@hotel.com
   - Billing and financial reports
   - Payment processing

7. **Guest Users**: john@email.com, jane@email.com
   - Make reservations
   - View their bookings

## Testing the System

### 1. Test Reservation Workflow

- Login as Guest (john@email.com)
- Go to "New Reservation"
- Select dates and available room
- Complete reservation

### 2. Test Admin Functions

- Login as Admin (admin@hotel.com)
- View Dashboard statistics
- Manage users and settings
- View activity logs

### 3. Test Housekeeping

- Login as Manager
- Create housekeeping tasks
- Assign to housekeeping staff
- Login as housekeeping staff to complete tasks

### 4. Test POS System

- Login as Chef or Manager
- Go to POS System
- Add menu items to cart
- Process order

### 5. Test Billing

- Login as Accountant
- Create bills for guests
- Process payments
- Generate receipts

## Project Structure Summary

```
Hotelmanagement/
├── frontend/              # Vue.js application
│   ├── src/
│   │   ├── pages/        # Page components
│   │   ├── components/   # Reusable components
│   │   ├── stores/       # Pinia state management
│   │   ├── services/     # API service
│   │   └── router/       # Vue Router config
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
│
├── backend/              # PHP REST API
│   ├── public/          # Web root
│   │   └── index.php    # Entry point
│   ├── app/
│   │   ├── controllers/ # Request handlers
│   │   ├── models/      # Database models
│   │   ├── middleware/  # Auth & permissions
│   │   └── services/    # Business logic
│   ├── routes/          # API routes
│   ├── config/          # Configuration
│   └── .env.example
│
├── database/            # Database files
│   ├── schema.sql       # Tables & structure
│   └── seeds/
│       └── demo_data.sql # Sample data
│
└── docs/                # Documentation
    ├── SETUP.md         # Detailed setup
    └── API.md           # API endpoints
```

## Key Features

✅ **Multi-role access control** - 6 different user roles  
✅ **Complete reservation system** - Full lifecycle management  
✅ **Room management** - Status tracking and assignment  
✅ **Housekeeping module** - Task assignment and tracking  
✅ **POS system** - Restaurant orders and kitchen display  
✅ **Billing system** - Multiple payment types and receipts  
✅ **Inventory management** - Stock tracking and alerts  
✅ **Staff management** - Attendance, leave, payroll  
✅ **Admin dashboard** - Statistics and activity logs  
✅ **Audit logging** - Complete activity tracking  

## Troubleshooting

### Backend Issues

**1. Database connection failed**
- Check MySQL is running
- Verify credentials in .env
- Ensure database exists

**2. API endpoints return 404**
- Check PHP server is running on port 8000
- Verify routes in `routes/api.php`
- Check request URL format

**3. JWT token errors**
- Generate new JWT_SECRET in .env
- Check token is being sent in Authorization header
- Verify token hasn't expired (default 1 hour)

### Frontend Issues

**1. Page won't load**
- Run `npm install` to ensure dependencies
- Clear browser cache
- Check browser console for errors

**2. API calls fail**
- Verify backend is running
- Check CORS headers
- Inspect Network tab in DevTools

**3. Login not working**
- Verify database has demo data
- Check password is "password123"
- Review browser console for errors

## Performance Tips

1. **Database indexing** - Already optimized in schema
2. **API caching** - Consider adding Redis
3. **Frontend optimization** - Routes are code-split
4. **Production build** - Run `npm run build` for minified assets

## Security Notes

For production deployment:

1. Change all default passwords
2. Generate strong JWT_SECRET
3. Enable HTTPS
4. Configure CORS properly
5. Implement rate limiting
6. Add input validation on backend
7. Use environment variables for sensitive data
8. Implement 2FA for admin users
9. Regular security audits
10. Database backups

## Next Steps

1. **Customize branding** - Update hotel name and colors
2. **Add payment gateway** - Integrate Stripe/PayPal
3. **Setup email** - Configure SMTP for notifications
4. **Mobile app** - Consider React Native version
5. **Analytics** - Add reporting dashboard
6. **Multi-language** - Implement i18n

## Support Resources

- **API Docs**: See `docs/API.md`
- **Setup Guide**: See `docs/SETUP.md`
- **Code Comments**: Inline documentation in files
- **Database Schema**: See `database/schema.sql`

## Success Checklist

- [ ] Database created and populated
- [ ] Backend server running on port 8000
- [ ] Frontend development server running on port 5173
- [ ] Can login with demo credentials
- [ ] Can view dashboard
- [ ] Can create a reservation
- [ ] Can view activity logs

Once all items are checked, the system is ready for use!

---

**Version**: 1.0.0  
**Last Updated**: January 2026  
**License**: Proprietary
