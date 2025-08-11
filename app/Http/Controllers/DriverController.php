<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DriverController extends Controller
{
    public function index(): JsonResponse
    {
        $drivers = Driver::with('vehicleDriverAssignments')->get();

        return response()->json([
            'success' => true,
            'data' => $drivers,
        ]);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Create form data',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $validated['is_active'] ?? true;

            $driver = Driver::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Driver created successfully',
                'data' => $driver,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(Driver $driver): JsonResponse
    {
        $driver->load('vehicleDriverAssignments.booking');

        return response()->json([
            'success' => true,
            'data' => $driver,
        ]);
    }

    public function edit(Driver $driver): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $driver,
        ]);
    }

    public function update(Request $request, Driver $driver): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ]);

            $driver->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Driver updated successfully',
                'data' => $driver,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Driver $driver): JsonResponse
    {
        if ($driver->vehicleDriverAssignments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete driver with associated assignments',
            ], 422);
        }

        $driver->delete();

        return response()->json([
            'success' => true,
            'message' => 'Driver deleted successfully',
        ]);
    }

    /**
     * Toggle driver active status
     */
    public function toggleStatus(Driver $driver): JsonResponse
    {
        $driver->update(['is_active' => !$driver->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Driver status updated successfully',
            'data' => $driver,
        ]);
    }

    /**
     * Get active drivers only
     */
    public function getActiveDrivers(): JsonResponse
    {
        $drivers = Driver::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $drivers,
        ]);
    }
}
