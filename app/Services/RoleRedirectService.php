<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleRedirectService
{
    /**
     * Get the appropriate redirect URL based on user role
     */
    public static function getRedirectUrl(): string
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('No authenticated user found');
            return '/dashboard';
        }

        if (!$user->role) {
            Log::warning('User has no role assigned', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->full_name ?? 'N/A'
            ]);
            return '/dashboard';
        }

        $roleName = $user->role->name;
        Log::info('User login redirect', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->full_name ?? 'N/A',
            'role_id' => $user->role->id,
            'role' => $roleName
        ]);

        $redirectUrl = match ($roleName) {
            'Admin' => '/admin/dashboard',
            'Admin Staff' => '/admin-staff/dashboard',
            'Approver' => '/approver/dashboard',
            'User' => '/requester/dashboard',
            default => '/dashboard'
        };

        Log::info('Redirect URL determined', [
            'user_id' => $user->id,
            'role' => $roleName,
            'redirect_url' => $redirectUrl
        ]);

        return $redirectUrl;
    }

    /**
     * Get the appropriate Livewire component based on user role
     */
    public static function getDashboardComponent(): string
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return 'dashboard';
        }

        $roleName = $user->role->name;

        return match ($roleName) {
            'Admin' => 'admin.dashboard',
            'Admin Staff' => 'admin-staff.dashboard',
            'Approver' => 'approver.dashboard',
            'User' => 'requester.dashboard',
            default => 'dashboard'
        };
    }

    /**
     * Check if user has access to a specific role section
     */
    public static function hasRoleAccess(string $role): bool
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return false;
        }

        $userRole = $user->role->name;
        $allowedRoles = explode(',', $role);

        return in_array($userRole, $allowedRoles);
    }

    /**
     * Get role-specific menu items
     */
    public static function getMenuItems(): array
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return [];
        }

        $roleName = $user->role->name;

        if ($roleName === 'User') {
            return [
                ['name' => 'My Dashboard', 'url' => '/requester/my-dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ['name' => 'My Bookings', 'url' => '/requester/bookings', 'icon' => 'fas fa-calendar'],
                ['name' => 'New Booking', 'url' => '/requester/bookings/create', 'icon' => 'fas fa-plus'],
            ];
        }

        return match ($roleName) {
            'Admin' => [
                ['name' => 'Admin Dashboard', 'url' => '/admin/dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ['name' => 'Manage Users', 'url' => '/admin/users', 'icon' => 'fas fa-users'],
                ['name' => 'All Bookings', 'url' => '/admin/bookings', 'icon' => 'fas fa-calendar-alt'],
                ['name' => 'Manage Assets', 'url' => '/admin/asset-types', 'icon' => 'fas fa-car'],
                ['name' => 'Analytics', 'url' => '/admin/analytics', 'icon' => 'fas fa-chart-line'],
                ['name' => 'Approvers', 'url' => '/admin/approvers', 'icon' => 'fas fa-user-check'],
            ],
            'Admin Staff' => [
                ['name' => 'Staff Dashboard', 'url' => '/admin-staff/dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ['name' => 'Manage Bookings', 'url' => '/admin-staff/bookings', 'icon' => 'fas fa-calendar-check'],
                ['name' => 'Vehicles', 'url' => '/admin-staff/vehicles', 'icon' => 'fas fa-car'],
                ['name' => 'Driver Assignments', 'url' => '/admin-staff/assignments', 'icon' => 'fas fa-user-tie'],
            ],
            'Approver' => [
                ['name' => 'Approver Dashboard', 'url' => '/approver/dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ['name' => 'Pending Approvals', 'url' => '/approver/approvals/pending', 'icon' => 'fas fa-clock'],
                ['name' => 'Approval History', 'url' => '/approver/approvals/my-history', 'icon' => 'fas fa-history'],
                ['name' => 'My Bookings', 'url' => '/requester/my-dashboard', 'icon' => 'fas fa-calendar'],
            ],
            default => []
        };
    }

    /**
     * Get the user's role display name
     */
    public static function getRoleDisplayName(): string
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return 'User';
        }

        return $user->role->name;
    }
}
