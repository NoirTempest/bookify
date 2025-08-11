<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Approver;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the approver dashboard with pending approvals and statistics
     */
    public function index(): JsonResponse
    {
        try {
            $userId = Auth::id();

            // Get approver records for current user
            $approverRecords = Approver::where('user_id', $userId)->pluck('id');

            if ($approverRecords->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'statistics' => [
                            'pending_approvals' => 0,
                            'approved_today' => 0,
                            'rejected_today' => 0,
                            'total_processed' => 0,
                        ],
                        'pending_bookings' => [],
                        'recent_approvals' => [],
                        'approval_hierarchy' => [],
                    ]
                ]);
            }

            // Get pending bookings that need approval from this user
            $pendingBookings = Booking::with(['user', 'assetType', 'assetDetail'])
                ->where('status', 'pending')
                ->whereHas('assetType.approvers', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->limit(20)
                ->get();

            // Statistics
            $pendingApprovalsCount = $pendingBookings->count();

            $approvedToday = ApprovalLog::join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->where('approval_logs.status', 'approved')
                ->whereDate('approval_logs.created_at', now()->toDateString())
                ->count();

            $rejectedToday = ApprovalLog::join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->where('approval_logs.status', 'rejected')
                ->whereDate('approval_logs.created_at', now()->toDateString())
                ->count();

            $totalProcessed = ApprovalLog::join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->whereIn('approval_logs.status', ['approved', 'rejected'])
                ->count();

            $recentApprovals = ApprovalLog::with(['booking.user', 'booking.assetType', 'booking.assetDetail'])
                ->join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->orderBy('approval_logs.created_at', 'desc')
                ->limit(10)
                ->get(['approval_logs.*']);

            // Get approval hierarchy for assets this user can approve
            $approvalHierarchy = Approver::with(['assetType', 'user'])
                ->where('user_id', $userId)
                ->get()
                ->groupBy('asset_type_id');

            $statistics = [
                'pending_approvals' => $pendingApprovalsCount,
                'approved_today' => $approvedToday,
                'rejected_today' => $rejectedToday,
                'total_processed' => $totalProcessed,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $statistics,
                    'pending_bookings' => $pendingBookings,
                    'recent_approvals' => $recentApprovals,
                    'approval_hierarchy' => $approvalHierarchy,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed approval statistics for a date range
     */
    public function getApprovalStats(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $userId = Auth::id();
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Approval counts by status in the period
            $approvalStats = ApprovalLog::join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->whereBetween('approval_logs.created_at', [$startDate, $endDate])
                ->selectRaw('approval_logs.status, COUNT(*) as count')
                ->groupBy('approval_logs.status')
                ->get()
                ->pluck('count', 'status');

            // Daily approval counts
            $dailyApprovals = ApprovalLog::join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->whereBetween('approval_logs.created_at', [$startDate, $endDate])
                ->selectRaw('DATE(approval_logs.created_at) as date, approval_logs.status, COUNT(*) as count')
                ->groupBy('date', 'approval_logs.status')
                ->orderBy('date')
                ->get()
                ->groupBy('date');

            // Asset type breakdown
            $assetTypeBreakdown = ApprovalLog::with('booking.assetType')
                ->join('approvers', 'approval_logs.approver_id', '=', 'approvers.id')
                ->where('approvers.user_id', $userId)
                ->whereBetween('approval_logs.created_at', [$startDate, $endDate])
                ->get()
                ->groupBy('booking.assetType.name')
                ->map(function ($logs) {
                    return [
                        'total' => $logs->count(),
                        'approved' => $logs->where('status', 'approved')->count(),
                        'rejected' => $logs->where('status', 'rejected')->count(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    'approval_stats' => $approvalStats,
                    'daily_approvals' => $dailyApprovals,
                    'asset_type_breakdown' => $assetTypeBreakdown,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating approval statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
