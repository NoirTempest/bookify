<?php

namespace App\Http\Controllers;

use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BusinessUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $businessUnits = BusinessUnit::with('users')->get();

        return response()->json([
            'success' => true,
            'data' => $businessUnits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return form data if needed for web interface
        return response()->json([
            'success' => true,
            'message' => 'Create form data',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:business_units,name',
            ]);

            $businessUnit = BusinessUnit::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Business unit created successfully',
                'data' => $businessUnit,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessUnit $businessUnit): JsonResponse
    {
        $businessUnit->load('users');

        return response()->json([
            'success' => true,
            'data' => $businessUnit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessUnit $businessUnit): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $businessUnit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessUnit $businessUnit): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:business_units,name,' . $businessUnit->id,
            ]);

            $businessUnit->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Business unit updated successfully',
                'data' => $businessUnit,
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
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessUnit $businessUnit): JsonResponse
    {
        // Check if business unit has users
        if ($businessUnit->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete business unit with associated users',
            ], 422);
        }

        $businessUnit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business unit deleted successfully',
        ]);
    }
}
