<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Requester\DashboardController as RequesterDashboardController;
use App\Http\Controllers\Requester\BookingController as RequesterBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Approver\DashboardController as ApproverDashboardController;
use App\Http\Controllers\Approver\ApprovalController;
use App\Livewire\Requester\Status;

/*
|--------------------------------------------------------------------------
| Role-Based Routes
|--------------------------------------------------------------------------
|
| Here we organize routes by user roles: Requester, Admin, and Approver
| Each role has their own set of controllers and views
|
*/

// Requester Routes
Route::prefix('requester')->name('requester.')->middleware(['auth', 'verified'])->group(function () {
    // Conference room status (Livewire component)
    Route::get('/status', App\Livewire\Requester\Status::class)->name('status');

    // Dashboard - Use Livewire component for web interface
    Route::get('/dashboard', App\Livewire\Requester\Dashboard::class)->name('dashboard');

    // Main bookings route (Livewire component for list view)
    Route::get('/bookings', App\Livewire\Requester\Bookings::class)->name('bookings');

    // Calendar route (Livewire component)
    Route::get('/calendar', App\Livewire\Requester\Calendar::class)->name('calendar');

    // Notifications route (Livewire component)
    Route::get('/notifications', App\Livewire\Requester\Notifications::class)->name('notifications');

    // Bookings Management
    Route::prefix('bookings')->name('bookings.')->group(function () {
        // Note: the main bookings index route is defined above, this is for API/sub-routes
        // Route::get('/', [RequesterBookingController::class, 'index'])->name('index');
        Route::get('/create', function () {
            return view('requester.bookings.create');
        })->name('create');
        Route::post('/', [RequesterBookingController::class, 'store'])->name('store');
        Route::get('/{id}', [RequesterBookingController::class, 'show'])->name('show');
        Route::put('/{id}', [RequesterBookingController::class, 'update'])->name('update');
        Route::patch('/{id}/cancel', [RequesterBookingController::class, 'cancel'])->name('cancel');
        Route::get('/available-assets', [RequesterBookingController::class, 'getAvailableAssets'])->name('available-assets');
    });

    // Livewire Routes
    Route::get('/my-dashboard', App\Livewire\Requester\Dashboard::class)->name('livewire.dashboard');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:Admin'])->group(function () {

    // Dashboard - Use Livewire component for web interface
    Route::get('/dashboard', App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');

    // Livewire Dashboard
    Route::get('/livewire-dashboard', App\Livewire\Admin\Dashboard::class)->name('livewire.dashboard');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
        Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
        Route::patch('/{id}/password', [UserManagementController::class, 'updatePassword'])->name('update-password');
        Route::patch('/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::get('/reference-data', [UserManagementController::class, 'getReferenceData'])->name('reference-data');
        Route::patch('/bulk-update-status', [UserManagementController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    });

    // Asset Management (using existing controllers)
    Route::resource('asset-types', App\Http\Controllers\AssetTypeController::class);
    Route::resource('asset-details', App\Http\Controllers\AssetDetailController::class);

    // Booking Management (admin view of all bookings)
    Route::resource('bookings', App\Http\Controllers\BookingController::class);

    // Reference Data Management
    Route::resource('business-units', App\Http\Controllers\BusinessUnitController::class);
    Route::resource('company-codes', App\Http\Controllers\CompanyCodeController::class);
    Route::resource('branches', App\Http\Controllers\BranchController::class);
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);

    // Approver Management
    Route::resource('approvers', App\Http\Controllers\ApproverController::class);

    // Driver Management
    Route::resource('drivers', App\Http\Controllers\DriverController::class);
    Route::resource('vehicle-assignments', App\Http\Controllers\VehicleDriverAssignmentController::class);
});

// Approver Routes
Route::prefix('approver')->name('approver.')->middleware(['auth', 'verified', 'role:Manager,Admin'])->group(function () {

    // Dashboard - Use Livewire component for web interface
    Route::get('/dashboard', App\Livewire\Approver\Dashboard::class)->name('dashboard');
    Route::get('/approval-stats', [ApproverDashboardController::class, 'getApprovalStats'])->name('approval-stats');

    // Livewire Dashboard
    Route::get('/livewire-dashboard', App\Livewire\Approver\Dashboard::class)->name('livewire.dashboard');

    // Approval Management
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/pending', [ApprovalController::class, 'getPendingApprovals'])->name('pending');
        Route::patch('/{bookingId}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::patch('/{bookingId}/reject', [ApprovalController::class, 'reject'])->name('reject');
        Route::get('/{bookingId}/history', [ApprovalController::class, 'getApprovalHistory'])->name('history');
        Route::get('/workflow/{assetTypeId}', [ApprovalController::class, 'getApprovalWorkflow'])->name('workflow');
        Route::patch('/bulk-approve', [ApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::get('/my-history', [ApprovalController::class, 'getMyApprovalHistory'])->name('my-history');
    });
});

// Shared Routes (accessible by multiple roles)
Route::middleware(['auth', 'verified'])->group(function () {

    // Asset Files (for viewing/downloading)
    Route::resource('asset-files', App\Http\Controllers\AssetFileController::class);

    // Booked Guests
    Route::resource('booked-guests', App\Http\Controllers\BookedGuestController::class);

    // Approval Logs (read-only for tracking)
    Route::get('approval-logs', [App\Http\Controllers\ApprovalLogController::class, 'index'])->name('approval-logs.index');
    Route::get('approval-logs/{id}', [App\Http\Controllers\ApprovalLogController::class, 'show'])->name('approval-logs.show');
});

// API Routes for AJAX/Frontend Integration
Route::prefix('api')->name('api.')->middleware(['auth:sanctum'])->group(function () {

    // Requester API
    Route::prefix('requester')->name('requester.')->group(function () {
        Route::get('/dashboard', [RequesterDashboardController::class, 'index']);
        Route::apiResource('bookings', RequesterBookingController::class);
        Route::get('/bookings/{id}/available-assets', [RequesterBookingController::class, 'getAvailableAssets']);
    });

    // Admin API
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/analytics', [AdminDashboardController::class, 'analytics']);
        Route::apiResource('users', UserManagementController::class);
    });

    // Approver API
    Route::prefix('approver')->name('approver.')->middleware(['role:Manager,Admin'])->group(function () {
        Route::get('/dashboard', [ApproverDashboardController::class, 'index']);
        Route::get('/pending-approvals', [ApprovalController::class, 'getPendingApprovals']);
        Route::patch('/approve/{bookingId}', [ApprovalController::class, 'approve']);
        Route::patch('/reject/{bookingId}', [ApprovalController::class, 'reject']);
    });

    // Profile API (available to all authenticated users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'show']);
        Route::patch('/update', [App\Http\Controllers\ProfileController::class, 'updateProfile']);
        Route::patch('/password', [App\Http\Controllers\ProfileController::class, 'changePassword']);
        Route::get('/bookings', [App\Http\Controllers\ProfileController::class, 'getUserBookings']);
        Route::get('/pending-approvals', [App\Http\Controllers\ProfileController::class, 'getPendingApprovals']);
        Route::get('/approval-history', [App\Http\Controllers\ProfileController::class, 'getApprovalHistory']);
        Route::get('/dashboard-stats', [App\Http\Controllers\ProfileController::class, 'getDashboardStats']);
        Route::get('/recent-activities', [App\Http\Controllers\ProfileController::class, 'getRecentActivities']);
    });
});
