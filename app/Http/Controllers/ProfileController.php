<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Get current user profile (API)
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load([
            'branch',
            'department',
            'businessUnit',
            'companyCode',
            'role'
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update profile via API
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:15|unique:users,mobile_number,' . $request->user()->id,
                'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
            ]);

            $user = $request->user();
            $user->update($validated);

            $user->load([
                'branch',
                'department',
                'businessUnit',
                'companyCode',
                'role'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Change password via API
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                ], 422);
            }

            $user->update(['password' => Hash::make($validated['new_password'])]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Get user's bookings
     */
    public function getUserBookings(Request $request): JsonResponse
    {
        $user = $request->user();
        $bookings = $user->bookings()
            ->with(['assetType', 'assetDetail', 'bookedGuests', 'approvalLogs.approver.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * Get user's pending approvals (if user is an approver)
     */
    public function getPendingApprovals(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get all approver records for this user
        $approverRecords = $user->approvers;

        if ($approverRecords->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'User is not an approver',
            ]);
        }

        $pendingBookings = collect();

        foreach ($approverRecords as $approver) {
            $bookings = \App\Models\Booking::where('status', 'pending')
                ->where('asset_type_id', $approver->asset_type_id)
                ->whereDoesntHave('approvalLogs', function ($query) use ($approver) {
                    $query->where('approver_id', $approver->id);
                })
                ->with(['user', 'assetDetail', 'assetType'])
                ->get();

            $pendingBookings = $pendingBookings->merge($bookings);
        }

        return response()->json([
            'success' => true,
            'data' => $pendingBookings->unique('id')->values(),
        ]);
    }

    /**
     * Get user's approval history
     */
    public function getApprovalHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        $approverRecords = $user->approvers;

        if ($approverRecords->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'User is not an approver',
            ]);
        }

        $approvalLogs = \App\Models\ApprovalLog::whereIn('approver_id', $approverRecords->pluck('id'))
            ->with(['booking.user', 'booking.assetDetail', 'approver.assetType'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $approvalLogs,
        ]);
    }

    /**
     * Get user dashboard stats
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'pending_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'approved_bookings' => $user->bookings()->where('status', 'approved')->count(),
            'declined_bookings' => $user->bookings()->where('status', 'declined')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
        ];

        // Add approval stats if user is an approver
        $approverRecords = $user->approvers;
        if ($approverRecords->isNotEmpty()) {
            $stats['is_approver'] = true;
            $stats['pending_approvals'] = \App\Models\Booking::where('status', 'pending')
                ->whereIn('asset_type_id', $approverRecords->pluck('asset_type_id'))
                ->whereDoesntHave('approvalLogs', function ($query) use ($approverRecords) {
                    $query->whereIn('approver_id', $approverRecords->pluck('id'));
                })
                ->count();
            $stats['total_approvals_made'] = \App\Models\ApprovalLog::whereIn('approver_id', $approverRecords->pluck('id'))->count();
        } else {
            $stats['is_approver'] = false;
            $stats['pending_approvals'] = 0;
            $stats['total_approvals_made'] = 0;
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get user's recent activities
     */
    public function getRecentActivities(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->get('limit', 10);

        // Get recent bookings
        $recentBookings = $user->bookings()
            ->with(['assetDetail', 'assetType'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'type' => 'booking',
                    'action' => 'created',
                    'description' => "Created booking for {$booking->assetDetail->asset_name}",
                    'status' => $booking->status,
                    'date' => $booking->created_at,
                    'data' => $booking,
                ];
            });

        // Get recent approvals made by user (if approver)
        $recentApprovals = collect();
        $approverRecords = $user->approvers;

        if ($approverRecords->isNotEmpty()) {
            $recentApprovals = \App\Models\ApprovalLog::whereIn('approver_id', $approverRecords->pluck('id'))
                ->with(['booking.user', 'booking.assetDetail'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($log) {
                    return [
                        'type' => 'approval',
                        'action' => $log->status,
                        'description' => "Booking {$log->status} for {$log->booking->assetDetail->asset_name}",
                        'status' => $log->status,
                        'date' => $log->created_at,
                        'data' => $log,
                    ];
                });
        }

        // Combine and sort activities
        $activities = $recentBookings->merge($recentApprovals)
            ->sortByDesc('date')
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
