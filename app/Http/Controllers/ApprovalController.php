<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use App\Models\Approver;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ApprovalController extends Controller
{
    public function approve(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $userId = Auth::id();
        $assetTypeId = $booking->asset_type_id;

        $approver = Approver::where('user_id', $userId)
            ->where('asset_type_id', $assetTypeId)
            ->first();

        if (!$approver) {
            return back()->withErrors(['You are not authorized to approve this booking.']);
        }

        // Prevent double approval
        $existingLog = ApprovalLog::where('booking_id', $booking->id)
            ->where('approver_id', $approver->id)
            ->first();

        if ($existingLog) {
            return back()->withErrors(['You have already responded to this booking.']);
        }

        // Create approval log
        ApprovalLog::create([
            'booking_id' => $booking->id,
            'approver_id' => $approver->id,
            'status' => 'approved',
            'remarks' => $request->input('remarks', null),
            'approved_at' => Carbon::now(),
        ]);

        // Check if all levels approved
        $requiredLevels = Approver::where('asset_type_id', $assetTypeId)->count();
        $approvedLevels = ApprovalLog::where('booking_id', $booking->id)
            ->where('status', 'approved')
            ->count();

        if ($approvedLevels >= $requiredLevels) {
            $booking->status = 'approved';
            $booking->save();
        }

        return redirect()->back()->with('success', 'Booking approved successfully.');
    }

    public function reject(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $userId = Auth::id();
        $assetTypeId = $booking->asset_type_id;

        $approver = Approver::where('user_id', $userId)
            ->where('asset_type_id', $assetTypeId)
            ->first();

        if (!$approver) {
            return back()->withErrors(['You are not authorized to reject this booking.']);
        }

        // Prevent double rejection
        $existingLog = ApprovalLog::where('booking_id', $booking->id)
            ->where('approver_id', $approver->id)
            ->first();

        if ($existingLog) {
            return back()->withErrors(['You have already responded to this booking.']);
        }

        // Log rejection
        ApprovalLog::create([
            'booking_id' => $booking->id,
            'approver_id' => $approver->id,
            'status' => 'declined', // ✅ must match ENUM
            'remarks' => $request->input('remarks', null),
            'approved_at' => Carbon::now(),
        ]);

        // Rejection is final
        $booking->status = 'declined';
        $booking->save();

        return redirect()->back()->with('success', 'Booking rejected successfully.');
    }
}
