<?php

namespace App\Http\Controllers;

use App\Models\VehicleDriverAssignment;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\AssetDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class VehicleDriverAssignmentController extends Controller
{
    public function index(): JsonResponse
    {
        $assignments = VehicleDriverAssignment::with([
            'booking.user',
            'driver',
            'assetDetail',
            'assignedBy'
        ])->orderBy('assigned_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }

    public function create(): JsonResponse
    {
        $bookings = Booking::where('status', 'approved')
            ->with(['user', 'assetDetail'])
            ->get();
        $drivers = Driver::where('is_active', true)->get();
        $assetDetails = AssetDetail::all();
        $users = User::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings,
                'drivers' => $drivers,
                'asset_details' => $assetDetails,
                'users' => $users,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'driver_id' => 'required|exists:drivers,id',
                'asset_detail_id' => 'required|exists:asset_details,id',
                'assigned_date' => 'required|date',
                'assigned_by' => 'required|exists:users,id',
                'odometer_start' => 'nullable|numeric|min:0',
                'odometer_end' => 'nullable|numeric|min:0|gte:odometer_start',
            ]);

            // Check if booking is approved
            $booking = Booking::find($validated['booking_id']);
            if ($booking->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only assign drivers to approved bookings',
                ], 422);
            }

            // Check if driver is active
            $driver = Driver::find($validated['driver_id']);
            if (!$driver->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign inactive driver',
                ], 422);
            }

            // Check if assignment already exists for this booking
            $existingAssignment = VehicleDriverAssignment::where('booking_id', $validated['booking_id'])->first();
            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver already assigned to this booking',
                ], 422);
            }

            $assignment = VehicleDriverAssignment::create($validated);
            $assignment->load(['booking.user', 'driver', 'assetDetail', 'assignedBy']);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle driver assignment created successfully',
                'data' => $assignment,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(VehicleDriverAssignment $vehicleDriverAssignment): JsonResponse
    {
        $vehicleDriverAssignment->load([
            'booking.user',
            'driver',
            'assetDetail.assetType',
            'assignedBy'
        ]);

        return response()->json([
            'success' => true,
            'data' => $vehicleDriverAssignment,
        ]);
    }

    public function edit(VehicleDriverAssignment $vehicleDriverAssignment): JsonResponse
    {
        $bookings = Booking::where('status', 'approved')
            ->with(['user', 'assetDetail'])
            ->get();
        $drivers = Driver::where('is_active', true)->get();
        $assetDetails = AssetDetail::all();
        $users = User::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'assignment' => $vehicleDriverAssignment->load(['booking', 'driver', 'assetDetail', 'assignedBy']),
                'bookings' => $bookings,
                'drivers' => $drivers,
                'asset_details' => $assetDetails,
                'users' => $users,
            ],
        ]);
    }

    public function update(Request $request, VehicleDriverAssignment $vehicleDriverAssignment): JsonResponse
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'driver_id' => 'required|exists:drivers,id',
                'asset_detail_id' => 'required|exists:asset_details,id',
                'assigned_date' => 'required|date',
                'assigned_by' => 'required|exists:users,id',
                'odometer_start' => 'nullable|numeric|min:0',
                'odometer_end' => 'nullable|numeric|min:0|gte:odometer_start',
            ]);

            // Check if booking is approved
            $booking = Booking::find($validated['booking_id']);
            if ($booking->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only assign drivers to approved bookings',
                ], 422);
            }

            // Check if driver is active
            $driver = Driver::find($validated['driver_id']);
            if (!$driver->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign inactive driver',
                ], 422);
            }

            $vehicleDriverAssignment->update($validated);
            $vehicleDriverAssignment->load(['booking.user', 'driver', 'assetDetail', 'assignedBy']);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle driver assignment updated successfully',
                'data' => $vehicleDriverAssignment,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(VehicleDriverAssignment $vehicleDriverAssignment): JsonResponse
    {
        $vehicleDriverAssignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle driver assignment deleted successfully',
        ]);
    }

    /**
     * Update odometer readings
     */
    public function updateOdometer(Request $request, VehicleDriverAssignment $vehicleDriverAssignment): JsonResponse
    {
        try {
            $validated = $request->validate([
                'odometer_start' => 'nullable|numeric|min:0',
                'odometer_end' => 'nullable|numeric|min:0|gte:odometer_start',
            ]);

            $vehicleDriverAssignment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Odometer readings updated successfully',
                'data' => $vehicleDriverAssignment,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
