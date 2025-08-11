<?php

namespace App\Http\Controllers\Requester;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the requester dashboard with statistics
     */
    public function index(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            // Get booking statistics for the current user
            $totalBookings = Booking::where('user_id', $userId)->count();
            $pendingBookings = Booking::where('user_id', $userId)->where('status', 'pending')->count();
            $approvedBookings = Booking::where('user_id', $userId)->where('status', 'approved')->count();
            $rejectedBookings = Booking::where('user_id', $userId)->where('status', 'rejected')->count();
            $cancelledBookings = Booking::where('user_id', $userId)->where('status', 'cancelled')->count();

            // Get recent bookings (last 10)
            $recentBookings = Booking::with(['assetType', 'assetDetail', 'user'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get upcoming bookings
            $upcomingBookings = Booking::with(['assetType', 'assetDetail'])
                ->where('user_id', $userId)
                ->where('scheduled_date', '>=', now()->toDateString())
                ->where('status', 'approved')
                ->orderBy('scheduled_date', 'asc')
                ->orderBy('time_from', 'asc')
                ->limit(5)
                ->get();

            $statistics = [
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'approved_bookings' => $approvedBookings,
                'rejected_bookings' => $rejectedBookings,
                'cancelled_bookings' => $cancelledBookings,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $statistics,
                    'recent_bookings' => $recentBookings,
                    'upcoming_bookings' => $upcomingBookings,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading dashboard data'
            ], 500);
        }
    }
}