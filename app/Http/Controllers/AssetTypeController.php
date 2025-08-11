<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AssetTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $assetTypes = AssetType::with(['assetDetails', 'approvers'])->get();

        return response()->json([
            'success' => true,
            'data' => $assetTypes,
        ]);
    }

    public function create()
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
                'name' => 'required|string|max:255|unique:asset_types,name',
            ]);

            $assetType = AssetType::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Asset type created successfully',
                'data' => $assetType,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(AssetType $assetType): JsonResponse
    {
        $assetType->load(['assetDetails', 'bookings', 'approvers.user']);

        return response()->json([
            'success' => true,
            'data' => $assetType,
        ]);
    }

    public function edit(AssetType $assetType): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $assetType,
        ]);
    }

    public function update(Request $request, AssetType $assetType): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:asset_types,name,' . $assetType->id,
            ]);

            $assetType->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Asset type updated successfully',
                'data' => $assetType,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(AssetType $assetType): JsonResponse
    {
        // Check if asset type has related records
        if ($assetType->assetDetails()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete asset type with associated asset details',
            ], 422);
        }

        if ($assetType->bookings()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete asset type with associated bookings',
            ], 422);
        }

        $assetType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset type deleted successfully',
        ]);
    }
}
