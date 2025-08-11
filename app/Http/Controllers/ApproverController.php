<?php

namespace App\Http\Controllers;

use App\Models\Approver;
use App\Models\AssetType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ApproverController extends Controller
{
    public function index(): JsonResponse
    {
        $approvers = Approver::with(['assetType', 'user', 'approvalLogs'])->get();

        return response()->json([
            'success' => true,
            'data' => $approvers,
        ]);
    }

    public function create(): JsonResponse
    {
        $assetTypes = AssetType::all();
        $users = User::where('is_active', true)->with(['role'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'asset_types' => $assetTypes,
                'users' => $users,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_type_id' => 'required|exists:asset_types,id',
                'user_id' => 'required|exists:users,id',
                'approver_level' => 'required|integer|min:1|max:10',
            ]);

            // Check if user is active
            $user = User::find($validated['user_id']);
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign inactive user as approver',
                ], 422);
            }

            // Check if approver already exists for this asset type and user
            $existingApprover = Approver::where('asset_type_id', $validated['asset_type_id'])
                ->where('user_id', $validated['user_id'])
                ->first();

            if ($existingApprover) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already an approver for this asset type',
                ], 422);
            }

            // Check if approver level already exists for this asset type
            $levelExists = Approver::where('asset_type_id', $validated['asset_type_id'])
                ->where('approver_level', $validated['approver_level'])
                ->first();

            if ($levelExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approver level already exists for this asset type',
                ], 422);
            }

            $approver = Approver::create($validated);
            $approver->load(['assetType', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Approver created successfully',
                'data' => $approver,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(Approver $approver): JsonResponse
    {
        $approver->load(['assetType', 'user', 'approvalLogs.booking']);

        return response()->json([
            'success' => true,
            'data' => $approver,
        ]);
    }

    public function edit(Approver $approver): JsonResponse
    {
        $assetTypes = AssetType::all();
        $users = User::where('is_active', true)->with(['role'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'approver' => $approver->load(['assetType', 'user']),
                'asset_types' => $assetTypes,
                'users' => $users,
            ],
        ]);
    }

    public function update(Request $request, Approver $approver): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_type_id' => 'required|exists:asset_types,id',
                'user_id' => 'required|exists:users,id',
                'approver_level' => 'required|integer|min:1|max:10',
            ]);

            // Check if user is active
            $user = User::find($validated['user_id']);
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign inactive user as approver',
                ], 422);
            }

            // Check if approver already exists for this asset type and user (excluding current record)
            $existingApprover = Approver::where('asset_type_id', $validated['asset_type_id'])
                ->where('user_id', $validated['user_id'])
                ->where('id', '!=', $approver->id)
                ->first();

            if ($existingApprover) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already an approver for this asset type',
                ], 422);
            }

            // Check if approver level already exists for this asset type (excluding current record)
            $levelExists = Approver::where('asset_type_id', $validated['asset_type_id'])
                ->where('approver_level', $validated['approver_level'])
                ->where('id', '!=', $approver->id)
                ->first();

            if ($levelExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approver level already exists for this asset type',
                ], 422);
            }

            $approver->update($validated);
            $approver->load(['assetType', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Approver updated successfully',
                'data' => $approver,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Approver $approver): JsonResponse
    {
        // Check if approver has approval logs
        if ($approver->approvalLogs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete approver with existing approval logs',
            ], 422);
        }

        $approver->delete();

        return response()->json([
            'success' => true,
            'message' => 'Approver deleted successfully',
        ]);
    }

    /**
     * Get approvers for a specific asset type
     */
    public function getApproversByAssetType(AssetType $assetType): JsonResponse
    {
        $approvers = $assetType->approvers()
            ->with('user')
            ->orderBy('approver_level')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $approvers,
        ]);
    }

    /**
     * Get approvers for a specific user
     */
    public function getApproversByUser(User $user): JsonResponse
    {
        $approvers = $user->approvers()
            ->with('assetType')
            ->orderBy('approver_level')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $approvers,
        ]);
    }

    /**
     * Get next approver for a booking
     */
    public function getNextApprover(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_type_id' => 'required|exists:asset_types,id',
                'current_level' => 'nullable|integer|min:0',
            ]);

            $currentLevel = $validated['current_level'] ?? 0;

            $nextApprover = Approver::where('asset_type_id', $validated['asset_type_id'])
                ->where('approver_level', '>', $currentLevel)
                ->with('user')
                ->orderBy('approver_level')
                ->first();

            if (!$nextApprover) {
                return response()->json([
                    'success' => false,
                    'message' => 'No next approver found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $nextApprover,
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
     * Get approval hierarchy for an asset type
     */
    public function getApprovalHierarchy(AssetType $assetType): JsonResponse
    {
        $hierarchy = $assetType->approvers()
            ->with('user')
            ->orderBy('approver_level')
            ->get()
            ->map(function ($approver) {
                return [
                    'id' => $approver->id,
                    'level' => $approver->approver_level,
                    'user' => [
                        'id' => $approver->user->id,
                        'name' => $approver->user->full_name,
                        'email' => $approver->user->email,
                        'role' => $approver->user->role->name ?? null,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'asset_type' => $assetType,
                'approval_hierarchy' => $hierarchy,
            ],
        ]);
    }

    /**
     * Bulk create approvers for an asset type
     */
    public function bulkCreateApprovers(Request $request, AssetType $assetType): JsonResponse
    {
        try {
            $validated = $request->validate([
                'approvers' => 'required|array|min:1',
                'approvers.*.user_id' => 'required|exists:users,id',
                'approvers.*.approver_level' => 'required|integer|min:1|max:10',
            ]);

            // Check for duplicate levels
            $levels = array_column($validated['approvers'], 'approver_level');
            if (count($levels) !== count(array_unique($levels))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate approver levels are not allowed',
                ], 422);
            }

            // Check for duplicate users
            $userIds = array_column($validated['approvers'], 'user_id');
            if (count($userIds) !== count(array_unique($userIds))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate users are not allowed',
                ], 422);
            }

            // Delete existing approvers for this asset type
            $assetType->approvers()->delete();

            $createdApprovers = [];

            foreach ($validated['approvers'] as $approverData) {
                $approver = Approver::create([
                    'asset_type_id' => $assetType->id,
                    'user_id' => $approverData['user_id'],
                    'approver_level' => $approverData['approver_level'],
                ]);

                $approver->load('user');
                $createdApprovers[] = $approver;
            }

            return response()->json([
                'success' => true,
                'message' => 'Approvers created successfully',
                'data' => $createdApprovers,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
