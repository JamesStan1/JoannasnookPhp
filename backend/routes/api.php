<?php

class Router {
    private $routes = [];

    public function post($path, $controller, $method) {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function get($path, $controller, $method) {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function put($path, $controller, $method) {
        $this->routes['PUT'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function delete($path, $controller, $method) {
        $this->routes['DELETE'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch($method, $path) {
        if (!isset($this->routes[$method])) {
            return error('Method not allowed', 405);
        }

        // Check for exact match
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            return $this->callController($route['controller'], $route['method']);
        }

        // Check for parameterized routes
        foreach ($this->routes[$method] as $route_path => $route_info) {
            $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $route_path);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                // Extract parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $params[] = $value;
                    }
                }

                return $this->callController($route_info['controller'], $route_info['method'], $params);
            }
        }

        return error('Route not found', 404);
    }

    private function callController($controller, $method, $params = []) {
        $controllerClass = "App\\Controllers\\$controller";

        if (!class_exists($controllerClass)) {
            return error('Controller not found', 500);
        }

        $instance = new $controllerClass();

        if (!method_exists($instance, $method)) {
            return error('Method not found', 500);
        }

        if (!empty($params)) {
            return call_user_func_array([$instance, $method], $params);
        }

        return $instance->$method();
    }
}

// Initialize router
$router = new Router();

// Public routes (no auth)
$router->get('/api/public/rooms', 'RoomController', 'getAll');
$router->get('/api/public/event-packages', 'EventPackageController', 'getPublic');
$router->get('/api/public/settings', 'AdminController', 'getPublicSettings');

// Auth routes
$router->post('/api/auth/login', 'AuthController', 'login');
$router->post('/api/auth/register', 'AuthController', 'register');
$router->post('/api/auth/verify-token', 'AuthController', 'verifyToken');
$router->post('/api/auth/logout', 'AuthController', 'logout');

// Room routes
$router->get('/api/rooms', 'RoomController', 'getAll');
$router->get('/api/rooms/archived', 'RoomController', 'getArchived');
$router->get('/api/rooms/{id}', 'RoomController', 'getById');
$router->post('/api/rooms', 'RoomController', 'create');
$router->put('/api/rooms/{id}', 'RoomController', 'update');
$router->post('/api/rooms/{id}/update', 'RoomController', 'update');
$router->put('/api/rooms/{id}/status', 'RoomController', 'updateStatus');
$router->put('/api/rooms/{id}/archive', 'RoomController', 'archive');
$router->put('/api/rooms/{id}/restore', 'RoomController', 'restore');
$router->delete('/api/rooms/{id}', 'RoomController', 'forceDelete');

// Reservation routes
$router->post('/api/reservations/available-rooms', 'ReservationController', 'getAvailableRooms');
$router->post('/api/reservations', 'ReservationController', 'create');
$router->get('/api/reservations/{id}', 'ReservationController', 'getById');
$router->get('/api/reservations/guest/my-reservations', 'ReservationController', 'getGuestReservations');
$router->put('/api/reservations/{id}/approve', 'ReservationController', 'approve');
$router->put('/api/reservations/{id}/check-in', 'ReservationController', 'checkIn');
$router->put('/api/reservations/{id}/check-out', 'ReservationController', 'checkOut');
$router->put('/api/reservations/{id}/cancel', 'ReservationController', 'cancel');
$router->put('/api/reservations/{id}', 'ReservationController', 'update');
$router->get('/api/admin/reservations', 'ReservationController', 'adminGetAll');
$router->get('/api/admin/reservations/search-guests', 'ReservationController', 'searchGuests');
$router->get('/api/admin/reservations/stats', 'ReservationController', 'adminGetStats');
$router->get('/api/admin/reservations/booked-dates', 'ReservationController', 'adminGetBookedDates');
$router->post('/api/admin/reservations/walk-in', 'ReservationController', 'walkIn');

// Housekeeping routes
$router->get('/api/housekeeping/my-tasks', 'HousekeepingController', 'getStaffTasks');
$router->get('/api/housekeeping/pending-tasks', 'HousekeepingController', 'getPendingTasks');
$router->post('/api/housekeeping/tasks', 'HousekeepingController', 'createTask');
$router->put('/api/housekeeping/tasks/assign', 'HousekeepingController', 'assignTask');
$router->put('/api/housekeeping/tasks/{id}/complete', 'HousekeepingController', 'completeTask');
$router->get('/api/housekeeping/rooms/{id}/status', 'HousekeepingController', 'getRoomStatus');
$router->get('/api/housekeeping/notifications', 'HousekeepingController', 'getCleaningNotifications');
$router->put('/api/housekeeping/rooms/{id}/clean', 'HousekeepingController', 'markRoomClean');

// POS routes
$router->get('/api/pos/menu',              'POSController', 'getMenu');
$router->get('/api/pos/rooms',             'POSController', 'getOccupiedRooms');
$router->get('/api/pos/discounts',         'POSController', 'getDiscounts');
$router->get('/api/pos/orders',            'POSController', 'getOrders');
$router->post('/api/pos/checkout',         'POSController', 'checkout');
$router->post('/api/pos/receipt-preview',  'POSController', 'receiptPreview');
$router->get('/api/pos/orders',            'POSController', 'getOrders');

// Chef routes
$router->get('/api/chef/orders',                             'ChefController', 'getOrders');
$router->get('/api/chef/orders/all',                         'ChefController', 'getAllOrders');
$router->put('/api/chef/orders/{id}/status',                 'ChefController', 'updateStatus');
$router->put('/api/chef/orders/invoice/{invoiceId}/status',  'ChefController', 'updateInvoiceStatus');
$router->delete('/api/chef/orders/{id}',                     'ChefController', 'deleteOrder');

// Restaurant routes
$router->get('/api/restaurant/menu', 'RestaurantController', 'getMenuItems');
$router->get('/api/restaurant/menu/all', 'RestaurantController', 'getAllMenuItems');
$router->get('/api/restaurant/menu/category/{id}', 'RestaurantController', 'getByCategory');
$router->post('/api/restaurant/menu-items', 'RestaurantController', 'createMenuItem');
$router->put('/api/restaurant/menu-items/{id}', 'RestaurantController', 'updateMenuItem');
$router->delete('/api/restaurant/menu-items/{id}', 'RestaurantController', 'deleteMenuItem');
$router->get('/api/restaurant/menu-items/archived', 'RestaurantController', 'getArchivedMenuItems');
$router->put('/api/restaurant/menu-items/{id}/restore', 'RestaurantController', 'restoreMenuItem');
$router->delete('/api/restaurant/menu-items/{id}/force', 'RestaurantController', 'forceDeleteMenuItem');
$router->post('/api/restaurant/orders', 'RestaurantController', 'createOrder');
$router->get('/api/restaurant/orders', 'RestaurantController', 'getOrders');
$router->get('/api/restaurant/orders/{id}', 'RestaurantController', 'getOrderById');
$router->get('/api/restaurant/kitchen-orders', 'RestaurantController', 'getKitchenOrders');
$router->put('/api/restaurant/orders/{id}/status', 'RestaurantController', 'updateOrderStatus');

// Billing routes
$router->get('/api/bills', 'BillingController', 'getAllBills');
$router->post('/api/bills', 'BillingController', 'createBill');
$router->get('/api/bills/{id}', 'BillingController', 'getBillDetails');
$router->get('/api/guest/{id}/bills', 'BillingController', 'getGuestBills');
$router->put('/api/bills/{id}/payment', 'BillingController', 'recordPayment');
$router->get('/api/bills/{id}/receipt', 'BillingController', 'generateReceipt');

// Inventory routes (literal routes BEFORE parameterized)
$router->get('/api/inventory/low-stock', 'InventoryController', 'getLowStock');
$router->get('/api/inventory/archived', 'InventoryController', 'getArchived');
$router->get('/api/inventory/activity', 'InventoryController', 'getActivityLog');
$router->get('/api/inventory', 'InventoryController', 'getAll');
$router->post('/api/inventory', 'InventoryController', 'addItem');
$router->put('/api/inventory/{id}/stock', 'InventoryController', 'updateStock');
$router->put('/api/inventory/{id}/withdraw', 'InventoryController', 'withdrawItem');
$router->put('/api/inventory/{id}/restore', 'InventoryController', 'restoreItem');
$router->put('/api/inventory/{id}', 'InventoryController', 'updateItem');
$router->get('/api/inventory/{id}/history', 'InventoryController', 'getHistory');
$router->delete('/api/inventory/{id}/force', 'InventoryController', 'forceDeleteItem');
$router->delete('/api/inventory/{id}', 'InventoryController', 'deleteItem');

// Auth profile
$router->get('/api/auth/profile', 'AuthController', 'getProfile');
$router->put('/api/auth/profile', 'AuthController', 'updateProfile');
$router->post('/api/auth/avatar', 'AuthController', 'uploadAvatar');
$router->delete('/api/auth/avatar', 'AuthController', 'removeAvatar');

// Staff routes
$router->post('/api/staff/attendance/clock-in', 'StaffController', 'clockIn');
$router->post('/api/staff/attendance/clock-out', 'StaffController', 'clockOut');
$router->post('/api/attendance/qr-scan', 'StaffController', 'qrScanClock');
$router->post('/api/staff/attendance/qr-clock', 'StaffController', 'staffQrClock');
$router->get('/api/staff/attendance/today', 'StaffController', 'getAttendanceRecord');
$router->get('/api/staff/attendance/report', 'StaffController', 'getAttendanceReport');
$router->get('/api/staff/leaves/my', 'StaffController', 'getMyLeaves');
$router->get('/api/staff/leaves/all', 'StaffController', 'getAllLeavesAdmin');
$router->post('/api/staff/leaves', 'StaffController', 'requestLeave');
$router->get('/api/staff/leaves/pending', 'StaffController', 'getPendingLeaves');
$router->put('/api/staff/leaves/{id}/approve', 'StaffController', 'approveLeave');
$router->put('/api/staff/leaves/{id}/reject', 'StaffController', 'rejectLeave');
$router->get('/api/admin/payroll/weekly', 'StaffController', 'getWeeklyPayroll');
$router->post('/api/admin/payroll/rate', 'StaffController', 'setHourlyRate');
$router->get('/api/staff/{id}/payroll', 'StaffController', 'getPayroll');
$router->post('/api/staff/{id}/payroll/calculate', 'StaffController', 'calculatePayroll');
$router->post('/api/staff/{id}/payroll/payment', 'StaffController', 'recordPayment');

// Admin routes
$router->get('/api/admin/dashboard', 'AdminController', 'getDashboard');
$router->get('/api/admin/analytics', 'AdminController', 'getAnalytics');
$router->get('/api/admin/activity-logs', 'AdminController', 'getActivityLogs');
$router->get('/api/admin/settings', 'AdminController', 'getSettings');
$router->put('/api/admin/settings', 'AdminController', 'updateSettings');
$router->get('/api/admin/users', 'AdminController', 'getAllUsers');
$router->get('/api/admin/users/archived', 'AdminController', 'getArchivedUsers');
$router->post('/api/admin/users', 'AdminController', 'createUser');
$router->get('/api/admin/users/{id}/qr-code', 'AdminController', 'getUserQrCode');
$router->put('/api/admin/users/{id}', 'AdminController', 'updateUser');
$router->put('/api/admin/users/{id}/restore', 'AdminController', 'restoreUser');
$router->delete('/api/admin/users/{id}', 'AdminController', 'deleteUser');
$router->get('/api/admin/export', 'AdminController', 'exportReport');
$router->get('/api/admin/attendance', 'AdminController', 'getDailyAttendance');
$router->post('/api/admin/attendance/clock-in', 'AdminController', 'adminClockIn');
$router->put('/api/admin/attendance/{id}/clock-out', 'AdminController', 'adminClockOut');
$router->get('/api/admin/audit-logs', 'AdminController', 'getSalesAuditLogs');
$router->get('/api/admin/system-logs', 'AdminController', 'getSystemLogs');
$router->get('/api/admin/holidays', 'AdminController', 'getHolidays');
$router->post('/api/admin/holidays', 'AdminController', 'addHoliday');
$router->delete('/api/admin/holidays/{id}', 'AdminController', 'deleteHoliday');
$router->get('/api/admin/discounts', 'AdminController', 'getDiscounts');
$router->post('/api/admin/discounts', 'AdminController', 'addDiscount');
$router->delete('/api/admin/discounts/{id}', 'AdminController', 'deleteDiscount');
$router->put('/api/admin/discounts/{id}/default', 'AdminController', 'setDefaultDiscount');

// Pending Reservation routes (public create + admin management)
$router->post('/api/pending-reservations', 'PendingReservationController', 'create');
$router->get('/api/pending-reservations/check-duplicate', 'PendingReservationController', 'checkDuplicate');
$router->get('/api/pending-reservations', 'PendingReservationController', 'getAll');
$router->get('/api/pending-reservations/{id}', 'PendingReservationController', 'getById');
$router->put('/api/pending-reservations/{id}/approve', 'PendingReservationController', 'approve');
$router->put('/api/pending-reservations/{id}/reject', 'PendingReservationController', 'reject');

// Event Package routes
$router->get('/api/event-packages/active', 'EventPackageController', 'getActive');
$router->get('/api/event-packages', 'EventPackageController', 'getAll');
$router->post('/api/event-packages', 'EventPackageController', 'create');
$router->put('/api/event-packages/{id}', 'EventPackageController', 'update');
$router->post('/api/event-packages/{id}/update', 'EventPackageController', 'update');
$router->delete('/api/event-packages/{id}', 'EventPackageController', 'delete');

// Event routes (literal routes BEFORE parameterized)
$router->get('/api/events/stats', 'EventController', 'getStats');
$router->get('/api/events/booked-dates', 'EventController', 'getBookedDates');
$router->get('/api/events/by-date', 'EventController', 'getByDate');
$router->get('/api/events/archived', 'EventController', 'getArchived');
$router->get('/api/events', 'EventController', 'getAll');
$router->post('/api/events', 'EventController', 'create');
$router->get('/api/events/{id}', 'EventController', 'getById');
$router->put('/api/events/{id}/status', 'EventController', 'updateStatus');
$router->post('/api/events/{id}/payment', 'EventController', 'recordPayment');
$router->put('/api/events/{id}/restore', 'EventController', 'restore');
$router->put('/api/events/{id}', 'EventController', 'update');
$router->delete('/api/events/{id}/force', 'EventController', 'forceDelete');
$router->delete('/api/events/{id}', 'EventController', 'delete');

// Staff reports routes
$router->post('/api/staff/reports', 'ReportController', 'submitReport');
$router->get('/api/staff/reports/my', 'ReportController', 'getMyReports');
$router->get('/api/admin/reports', 'ReportController', 'getAllReports');
$router->put('/api/admin/reports/{id}/respond', 'ReportController', 'respondToReport');
$router->put('/api/admin/reports/{id}/status', 'ReportController', 'updateReportStatus');

// Front Desk Dashboard
$router->get('/api/front-desk/dashboard', 'FrontDeskController', 'getDashboard');

// IT Dashboard routes
$router->get('/api/it/system-logs',                        'AdminController', 'getSystemLogs');
$router->get('/api/it/audit-logs',                         'AdminController', 'getITAuditLogs');
$router->get('/api/it/staff-hourly-rates',                 'AdminController', 'getStaffHourlyRates');
$router->put('/api/it/staff-hourly-rates/{id}',            'AdminController', 'updateStaffHourlyRate');
$router->get('/api/it/forgot-password-requests',           'AdminController', 'getForgotPasswordRequests');
$router->post('/api/it/forgot-password-requests',          'AdminController', 'submitForgotPasswordRequest');
$router->put('/api/it/forgot-password-requests/{id}/resolve', 'AdminController', 'resolveForgotPasswordRequest');
$router->put('/api/it/staff-password/{id}',                'AdminController', 'resetStaffPassword');
$router->get('/api/it/reports',                            'AdminController', 'getITReports');
$router->put('/api/it/reports/{id}/status',                'AdminController', 'updateITReportStatus');

return $router;
