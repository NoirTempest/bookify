<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\AssetType;
use App\Models\Approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user.branch', 'user.department', 'assetType'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(7);

        // Attach 1st and 2nd level approvers
        foreach ($bookings as $booking) {
            $approvers = Approver::with('user')
                ->where('asset_type_id', $booking->asset_type_id)
                ->orderBy('approver_level')
                ->get();

            $firstApprover = $approvers->firstWhere('approver_level', 1)?->user;
            $secondApprover = $approvers->firstWhere('approver_level', 2)?->user;

            $booking->first_approver = $firstApprover
                ? $firstApprover->first_name . ' ' . $firstApprover->last_name
                : 'N/A';

            $booking->second_approver = $secondApprover
                ? $secondApprover->first_name . ' ' . $secondApprover->last_name
                : 'N/A';
        }

        $assetTypes = AssetType::all();

        return view('bookings.index', compact('bookings', 'assetTypes'));
    }

    public function store(Request $request)
    {
        try {
            // Get Vehicle asset type ID
            $vehicleTypeId = AssetType::where('name', 'Vehicle')->value('id');

            // Conditional validation
            $validated = $request->validate([
                'asset_type_id' => 'required|exists:asset_types,id',

                'asset_detail_id' => [
                    function ($attribute, $value, $fail) use ($request, $vehicleTypeId) {
                        if ((int) $request->asset_type_id !== (int) $vehicleTypeId && empty($value)) {
                            $fail('The ' . $attribute . ' field is required.');
                        }
                    },
                    'nullable',
                    'exists:asset_details,id',
                ],

                'destination' => 'required|string|max:255',
                'scheduled_date' => 'required|date',
                'time_from' => 'required|date_format:H:i:s',
                'time_to' => 'required|date_format:H:i:s|after:time_from',
                'purpose' => 'required|string|max:1000',
                'no_of_seats' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
                'guests' => 'nullable|array',
                'guests.*' => 'nullable|email',
            ]);

            // Create the booking
            $booking = new Booking();
            $booking->asset_type_id = $validated['asset_type_id'];
            $booking->asset_detail_id = $validated['asset_detail_id'] ?? null;
            $booking->destination = $validated['destination'];
            $booking->scheduled_date = $validated['scheduled_date'];
            $booking->time_from = $validated['time_from'];
            $booking->time_to = $validated['time_to'];
            $booking->purpose = $validated['purpose'];
            $booking->no_of_seats = $validated['no_of_seats'] ?? null;
            $booking->user_id = Auth::id();
            $booking->status = 'pending';
            $booking->asset_name = optional($booking->assetDetail)->asset_name;
            $booking->notes = $validated['notes'] ?? null;
            $booking->save();

            // Save guest emails if any
            if (!empty($validated['guests'])) {
                foreach ($validated['guests'] as $email) {
                    if (!empty($email)) {
                        \App\Models\BookedGuest::create([
                            'booking_id' => $booking->id,
                            'email' => $email,
                        ]);
                    }
                }
            }

            // Return success JSON if requested
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset reservation successfully created.',
                    'booking' => $booking,
                ]);
            }

            return redirect()->back()->with('success', 'Asset reservation successfully created.');

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Booking Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Server error: ' . $e->getMessage()
                ], 500);
            }
            abort(500, 'Something went wrong while saving your booking.');
        }
    }
    public function print(Booking $booking)
    {
        $booking->load([
            'user',
            'assetType',
            'bookedGuests',
            'vehicleAssignment.assetDetail',
            'vehicleAssignment.driver'
        ]);

        return view('livewire.admin-staff.print', compact('booking'));
    }


}
