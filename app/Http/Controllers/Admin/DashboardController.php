<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\AssetDetail;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with comprehensive statistics
     */
    public function index(): JsonResponse
    {
        try {
            // Overall system statistics
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $totalAssets = AssetDetail::count();
            $totalBookings = Booking::count();

            // Booking statistics
            $pendingBookings = Booking::where('status', 'pending')->count();
            $approvedBookings = Booking::where('status', 'approved')->count();
            $rejectedBookings = Booking::where('status', 'rejected')->count();
            $cancelledBookings = Booking::where('status', 'cancelled')->count();

            // Asset utilization
            $assetTypes = AssetType::withCount(['assetDetails', 'bookings'])->get();
            
            // Recent bookings
            $recentBookings = Booking::with(['user', 'assetType', 'assetDetail'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Booking trends (last 30 days)
            $bookingTrends = Booking::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Most active users (top 10)
            $activeUsers = User::withCount('bookings')
                ->orderBy('bookings_count', 'desc')
                ->limit(10)
                ->get();

            // Asset utilization by type
            $assetUtilization = AssetType::select('asset_types.name')
                ->withCount(['bookings as total_bookings'])
                ->withCount(['bookings as approved_bookings' => function ($query) {
                    $query->where('status', 'approved');
                }])
                ->get();

            // Upcoming bookings requiring attention
            $upcomingBookings = Booking::with(['user', 'assetType', 'assetDetail'])
                ->where('scheduled_date', '>=', now()->toDateString())
                ->where('status', 'approved')
                ->orderBy('scheduled_date', 'asc')
                ->orderBy('time_from', 'asc')
                ->limit(15)
                ->get();

            // System health metrics
            $systemHealth = [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_assets' => $totalAssets,
                'total_bookings' => $totalBookings,
                'utilization_rate' => $totalAssets > 0 ? round(($approvedBookings / $totalAssets) * 100, 2) : 0,
            ];

            $bookingStatistics = [
                'total' => $totalBookings,
                'pending' => $pendingBookings,
                'approved' => $approvedBookings,
                'rejected' => $rejectedBookings,
                'cancelled' => $cancelledBookings,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'system_health' => $systemHealth,
                    'booking_statistics' => $bookingStatistics,
                    'asset_types' => $assetTypes,
                    'recent_bookings' => $recentBookings,
                    'booking_trends' => $bookingTrends,
                    'most_active_users' => $activeUsers,
                    'asset_utilization' => $assetUtilization,
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

    /**
     * Get detailed analytics for a specific time period
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Bookings by status in the period
            $bookingsByStatus = Booking::select('status', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('status')
                ->get();

            // Daily booking counts
            $dailyBookings = Booking::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Asset type usage
            $assetTypeUsage = AssetType::select('asset_types.name')
                ->withCount(['bookings as bookings_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->having('bookings_count', '>', 0)
                ->orderBy('bookings_count', 'desc')
                ->get();

            // Top users by booking count
            $topUsers = User::select('users.first_name', 'users.last_name', 'users.email')
                ->withCount(['bookings as bookings_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->having('bookings_count', '>', 0)
                ->orderBy('bookings_count', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    'bookings_by_status' => $bookingsByStatus,
                    'daily_bookings' => $dailyBookings,
                    'asset_type_usage' => $assetTypeUsage,
                    'top_users' => $topUsers,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating analytics'
            ], 500);
        }
    }
}