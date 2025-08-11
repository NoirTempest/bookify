# Role-Based Application Structure

This Laravel application is organized using a role-based architecture that separates functionality by user roles: **Requesters**, **Admin Staff**, and **Approvers**.

## 📁 Folder Structure

### Controllers
```
app/Http/Controllers/
├── Requester/
│   ├── DashboardController.php      # Requester dashboard & statistics
│   └── BookingController.php        # Booking management for requesters
├── Admin/
│   ├── DashboardController.php      # Admin dashboard & analytics
│   └── UserManagementController.php # User management operations
├── Approver/
│   ├── DashboardController.php      # Approver dashboard & stats
│   └── ApprovalController.php       # Booking approval management
└── [Existing Controllers]           # Shared controllers (Profile, Assets, etc.)
```

### Livewire Components
```
app/Livewire/
├── Requester/
│   └── Dashboard.php               # Interactive requester dashboard
├── Admin/
│   └── [Future admin components]
├── Approver/
│   └── [Future approver components]
└── Pages/
    └── Auth/                       # Authentication components
```

### Views
```
resources/views/
├── requester/
│   └── [Requester-specific views]
├── admin/
│   └── [Admin-specific views]
├── approver/
│   └── [Approver-specific views]
└── livewire/
    ├── requester/
    │   └── dashboard.blade.php     # Requester dashboard view
    ├── admin/
    │   └── [Admin Livewire views]
    └── approver/
        └── [Approver Livewire views]
```

## 🎭 User Roles

### 1. **Requester** (Regular Users)
- **Purpose**: Create and manage their own bookings
- **Access**: 
  - Personal dashboard with booking statistics
  - Create new bookings
  - View/edit their own bookings
  - Cancel pending/approved bookings
  - Check asset availability

**Key Features:**
- Personal booking statistics
- Recent bookings overview
- Upcoming approved bookings
- Asset availability checker
- Guest email management

### 2. **Admin Staff**
- **Purpose**: System administration and user management
- **Access**:
  - System-wide dashboard with comprehensive analytics
  - User management (CRUD operations)
  - Asset management
  - Reference data management
  - Booking oversight
  - Approver assignment

**Key Features:**
- System health metrics
- User activity analytics
- Bulk user operations
- Asset utilization reports
- Comprehensive booking management

### 3. **Approvers** (Managers/Supervisors)
- **Purpose**: Review and approve/reject booking requests
- **Access**:
  - Approval dashboard
  - Pending approvals queue
  - Approval history
  - Multi-level approval workflow
  - Bulk approval operations

**Key Features:**
- Pending approvals overview
- Approval statistics
- Multi-level workflow support
- Bulk approval capabilities
- Approval history tracking

## 🛣️ Routes Organization

### Route Structure
```php
// Requester Routes - /requester/*
Route::prefix('requester')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [RequesterDashboardController::class, 'index']);
    Route::resource('bookings', RequesterBookingController::class);
    // ... more requester routes
});

// Admin Routes - /admin/*
Route::prefix('admin')->middleware(['auth', 'verified', 'role:Admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::resource('users', UserManagementController::class);
    // ... more admin routes
});

// Approver Routes - /approver/*
Route::prefix('approver')->middleware(['auth', 'verified', 'role:Manager,Admin'])->group(function () {
    Route::get('/dashboard', [ApproverDashboardController::class, 'index']);
    Route::resource('approvals', ApprovalController::class);
    // ... more approver routes
});
```

## 🔐 Security & Middleware

### Role-Based Access Control
- **RoleMiddleware**: Checks user roles before granting access
- **Route Protection**: Each role section is protected by appropriate middleware
- **Database-Level Security**: Controllers filter data based on user permissions

### Middleware Stack
```php
'role' => \App\Http\Middleware\RoleMiddleware::class,
```

## 📊 Database Schema Integration

### User Roles
The system uses the existing database schema with these key tables:
- `users` - Contains role_id foreign key
- `roles` - Defines available roles (Admin, Manager, User, Driver)
- `approvers` - Defines approval hierarchy
- `approval_logs` - Tracks approval actions

### Permission Logic
- **Requesters**: Can only access their own bookings
- **Admins**: Full system access
- **Approvers**: Access to bookings requiring their approval level

## 🚀 Key Benefits

### 1. **Separation of Concerns**
- Each role has dedicated controllers and views
- Clear responsibility boundaries
- Easier maintenance and testing

### 2. **Scalability**
- Easy to add new roles or modify existing ones
- Role-specific features can be developed independently
- Clean API endpoints for each role

### 3. **Security**
- Role-based access control at multiple levels
- Data isolation between roles
- Audit trail for all actions

### 4. **User Experience**
- Role-specific dashboards and interfaces
- Relevant functionality for each user type
- Streamlined workflows

## 🔧 Usage Examples

### For Requesters
```php
// Access requester dashboard
GET /requester/dashboard

// Create a new booking
POST /requester/bookings

// View my bookings
GET /requester/bookings
```

### For Admins
```php
// Access admin dashboard
GET /admin/dashboard

// Manage users
GET /admin/users
POST /admin/users
PUT /admin/users/{id}
```

### For Approvers
```php
// Access approver dashboard
GET /approver/dashboard

// Get pending approvals
GET /approver/approvals/pending

// Approve a booking
PATCH /approver/approvals/{bookingId}/approve
```

## 🎯 Future Enhancements

### Potential Additions
1. **Role Permissions**: More granular permission system
2. **Department-Based Access**: Restrict access by department
3. **Custom Dashboards**: Configurable dashboard widgets
4. **Notification System**: Role-based notifications
5. **Reporting Module**: Role-specific reports
6. **API Versioning**: Separate API versions for each role

### Extensibility
The structure is designed to easily accommodate:
- New user roles
- Additional permissions
- Role-specific features
- Custom workflows
- Integration with external systems

---

This role-based structure provides a solid foundation for a scalable, secure, and maintainable booking system with clear separation of responsibilities and user-focused interfaces.