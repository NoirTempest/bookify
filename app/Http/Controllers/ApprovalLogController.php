<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use App\Models\Booking;
use App\Models\Approver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ApprovalLogController extends Controller
{
    public function index(): JsonResponse
    {
        $approvalLogs = ApprovalLog::with([
            'booking.user',
            'booking.assetDetail',
            'approver.user'
        ])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $approvalLogs,
        ]);
    }

    public function create(): JsonResponse
    {
        $bookings = Booking::where('status', 'pending')
            ->with(['user', 'assetDetail'])
            ->get();
        $approvers = Approver::with(['user', 'assetType'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings,
                'approvers' => $approvers,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'approver_id' => 'required|exists:approvers,id',
                'status' => ['required', 'string', Rule::in(['pending', 'approved', 'declined'])],
                'remarks' => 'nullable|string',
            ]);

            // Check if booking is still pending
            $booking = Booking::find($validated['booking_id']);
            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only create approval logs for pending bookings',
                ], 422);
            }

            // Check if approver is for the correct asset type
            $approver = Approver::find($validated['approver_id']);
            if ($approver->asset_type_id !== $booking->asset_type_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approver is not authorized for this asset type',
                ], 422);
            }

            // Check if approval log already exists for this booking and approver
            $existingLog = ApprovalLog::where('booking_id', $validated['booking_id'])
                ->where('approver_id', $validated['approver_id'])
                ->first();

            if ($existingLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval log already exists for this booking and approver',
                ], 422);
            }

            DB::beginTransaction();

            $approvalData = [
                'booking_id' => $validated['booking_id'],
                'approver_id' => $validated['approver_id'],
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
            ];

            // Set approved_at if status is approved
            if ($validated['status'] === 'approved') {
                $approvalData['approved_at'] = now();
            }

            $approvalLog = ApprovalLog::create($approvalData);

            // Update booking status based on approval logic
            $this->updateBookingStatus($booking);

            DB::commit();

            $approvalLog->load(['booking.user', 'approver.user']);

            return response()->json([
                'success' => true,
                'message' => 'Approval log created successfully',
                'data' => $approvalLog,
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the approval log',
            ], 500);
        }
    }

    public function show(ApprovalLog $approvalLog): JsonResponse
    {
        $approvalLog->load([
            'booking.user',
            'booking.assetDetail',
            'approver.user'
        ]);

        return response()->json([
            'success' => true,
            'data' => $approvalLog,
        ]);
    }

    public function edit(ApprovalLog $approvalLog): JsonResponse
    {
        $bookings = Booking::with(['user', 'assetDetail'])->get();
        $approvers = Approver::with(['user', 'assetType'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'approval_log' => $approvalLog->load(['booking', 'approver']),
                'bookings' => $bookings,
                'approvers' => $approvers,
            ],
        ]);
    }

    public function update(Request $request, ApprovalLog $approvalLog): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'string', Rule::in(['pending', 'approved', 'declined'])],
                'remarks' => 'nullable|string',
            ]);

            // Don't allow updating if booking is not pending
            if ($approvalLog->booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update approval log for non-pending booking',
                ], 422);
            }

            DB::beginTransaction();

            $updateData = [
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
            ];

            // Set or clear approved_at based on status
            if ($validated['status'] === 'approved') {
                $updateData['approved_at'] = now();
            } else {
                $updateData['approved_at'] = null;
            }

            $approvalLog->update($updateData);

            // Update booking status based on approval logic
            $this->updateBookingStatus($approvalLog->booking);

            DB::commit();

            $approvalLog->load(['booking.user', 'approver.user']);

            return response()->json([
                'success' => true,
                'message' => 'Approval log updated successfully',
                'data' => $approvalLog,
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the approval log',
            ], 500);
        }
    }

    public function destroy(ApprovalLog $approvalLog): JsonResponse
    {
        // Only allow deletion if booking is still pending
        if ($approvalLog->booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete approval log for non-pending booking',
            ], 422);
        }

        DB::beginTransaction();

        $booking = $approvalLog->booking;
        $approvalLog->delete();

        // Update booking status after deletion
        $this->updateBookingStatus($booking);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Approval log deleted successfully',
        ]);
    }

    /**
     * Get approval logs for a specific booking
     */
    public function getLogsByBooking(Booking $booking): JsonResponse
    {
        $logs = $booking->approvalLogs()
            ->with('approver.user')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get approval logs for a specific approver
     */
    public function getLogsByApprover(Approver $approver): JsonResponse
    {
        $logs = $approver->approvalLogs()
            ->with('booking.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Process approval for a booking
     */
    public function processApproval(Request $request, Booking $booking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'approver_id' => 'required|exists:approvers,id',
                'status' => ['required', 'string', Rule::in(['approved', 'declined'])],
                'remarks' => 'nullable|string',
            ]);

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is not pending approval',
                ], 422);
            }

            $approver = Approver::find($validated['approver_id']);

            // Check if approver is authorized for this asset type
            if ($approver->asset_type_id !== $booking->asset_type_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approver is not authorized for this asset type',
                ], 422);
            }

            // Check if this approver has already processed this booking
            $existingLog = ApprovalLog::where('booking_id', $booking->id)
                ->where('approver_id', $approver->id)
                ->first();

            if ($existingLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'This approver has already processed this booking',
                ], 422);
            }

            DB::beginTransaction();

            $approvalData = [
                'booking_id' => $booking->id,
                'approver_id' => $approver->id,
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
            ];

            if ($validated['status'] === 'approved') {
                $approvalData['approved_at'] = now();
            }

            $approvalLog = ApprovalLog::create($approvalData);

            // Update booking status
            $this->updateBookingStatus($booking);

            DB::commit();

            $approvalLog->load(['booking.user', 'approver.user']);

            return response()->json([
                'success' => true,
                'message' => 'Approval processed successfully',
                'data' => [
                    'approval_log' => $approvalLog,
                    'booking_status' => $booking->fresh()->status,
                ],
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the approval',
            ], 500);
        }
    }

    /**
     * Get pending approvals for a specific approver
     */
    public function getPendingApprovals(Approver $approver): JsonResponse
    {
        $pendingBookings = Booking::where('status', 'pending')
            ->where('asset_type_id', $approver->asset_type_id)
            ->whereDoesntHave('approvalLogs', function ($query) use ($approver) {
                $query->where('approver_id', $approver->id);
            })
            ->with(['user', 'assetDetail'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingBookings,
        ]);
    }

    /**
     * Update booking status based on approval workflow
     */
    private function updateBookingStatus(Booking $booking): void
    {
        // Get all approvers for this asset type ordered by level
        $approvers = Approver::where('asset_type_id', $booking->asset_type_id)
            ->orderBy('approver_level')
            ->get();

        // Get all approval logs for this booking
        $approvalLogs = ApprovalLog::where('booking_id', $booking->id)->get();

        // Check if any approver declined
        $hasDeclined = $approvalLogs->where('status', 'declined')->count() > 0;

        if ($hasDeclined) {
            $booking->update(['status' => 'declined']);
            return;
        }

        // Check if all required approvers have approved
        $approvedApproverIds = $approvalLogs->where('status', 'approved')->pluck('approver_id');
        $requiredApproverIds = $approvers->pluck('id');

        $allApproved = $requiredApproverIds->diff($approvedApproverIds)->isEmpty();

        if ($allApproved) {
            $booking->update(['status' => 'approved']);
        }
        // If not all approved and none declined, status remains 'pending'
    }
}
