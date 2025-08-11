<?php

namespace App\Http\Controllers;

use App\Models\AssetDetail;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AssetDetailController extends Controller
{
    public function index(): JsonResponse
    {
        $assetDetails = AssetDetail::with(['assetType', 'assetFiles'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $assetDetails,
        ]);
    }

    public function create(): JsonResponse
    {
        $assetTypes = AssetType::all();
        
        return response()->json([
            'success' => true,
            'data' => [
                'asset_types' => $assetTypes,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_type_id' => 'required|exists:asset_types,id',
                'asset_name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'color' => 'required|string|max:100',
                'plate_number' => 'required|string|max:100|unique:asset_details,plate_number',
                'number_of_seats' => 'required|integer|min:1',
            ]);

            $assetDetail = AssetDetail::create($validated);
            $assetDetail->load('assetType');

            return response()->json([
                'success' => true,
                'message' => 'Asset detail created successfully',
                'data' => $assetDetail,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(AssetDetail $assetDetail): JsonResponse
    {
        $assetDetail->load(['assetType', 'assetFiles', 'bookings.user', 'vehicleDriverAssignments.driver']);
        
        return response()->json([
            'success' => true,
            'data' => $assetDetail,
        ]);
    }

    public function edit(AssetDetail $assetDetail): JsonResponse
    {
        $assetTypes = AssetType::all();
        
        return response()->json([
            'success' => true,
            'data' => [
                'asset_detail' => $assetDetail,
                'asset_types' => $assetTypes,
            ],
        ]);
    }

    public function update(Request $request, AssetDetail $assetDetail): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_type_id' => 'required|exists:asset_types,id',
                'asset_name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'color' => 'required|string|max:100',
                'plate_number' => 'required|string|max:100|unique:asset_details,plate_number,' . $assetDetail->id,
                'number_of_seats' => 'required|integer|min:1',
            ]);

            $assetDetail->update($validated);
            $assetDetail->load('assetType');

            return response()->json([
                'success' => true,
                'message' => 'Asset detail updated successfully',
                'data' => $assetDetail,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(AssetDetail $assetDetail): JsonResponse
    {
        // Check if asset detail has related bookings
        if ($assetDetail->bookings()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete asset detail with associated bookings',
            ], 422);
        }

        // Check if asset detail has related assignments
        if ($assetDetail->vehicleDriverAssignments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete asset detail with associated driver assignments',
            ], 422);
        }

        $assetDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset detail deleted successfully',
        ]);
    }
}
