<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    /**
     * Display all users with filtering options
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with(['role', 'businessUnit', 'companyCode', 'branch', 'department']);

            // Search by name or email
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by role
            if ($request->has('role_id') && $request->role_id != '') {
                $query->where('role_id', $request->role_id);
            }

            // Filter by business unit
            if ($request->has('business_unit_id') && $request->business_unit_id != '') {
                $query->where('business_unit_id', $request->business_unit_id);
            }

            // Filter by department
            if ($request->has('department_id') && $request->department_id != '') {
                $query->where('department_id', $request->department_id);
            }

            // Filter by status
            if ($request->has('is_active') && $request->is_active != '') {
                $query->where('is_active', $request->is_active === 'true');
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users'
            ], 500);
        }
    }

    /**
     * Store a new user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile_number' => 'required|string|max:15',
                'password' => 'required|string|min:8',
                'business_unit_id' => 'required|exists:business_units,id',
                'company_code_id' => 'required|exists:company_codes,id',
                'branch_id' => 'required|exists:branches,id',
                'department_id' => 'required|exists:departments,id',
                'role_id' => 'required|exists:roles,id',
                'is_active' => 'boolean',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $validated['is_active'] ?? true;

            $user = User::create($validated);
            $user->load(['role', 'businessUnit', 'companyCode', 'branch', 'department']);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user'
            ], 500);
        }
    }

    /**
     * Show a specific user
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::with([
                'role', 
                'businessUnit', 
                'companyCode', 
                'branch', 
                'department',
                'bookings' => function ($query) {
                    $query->with(['assetType', 'assetDetail'])
                          ->orderBy('created_at', 'desc')
                          ->limit(10);
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update a user
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'mobile_number' => 'required|string|max:15',
                'business_unit_id' => 'required|exists:business_units,id',
                'company_code_id' => 'required|exists:company_codes,id',
                'branch_id' => 'required|exists:branches,id',
                'department_id' => 'required|exists:departments,id',
                'role_id' => 'required|exists:roles,id',
                'is_active' => 'boolean',
            ]);

            $user->update($validated);
            $user->load(['role', 'businessUnit', 'companyCode', 'branch', 'department']);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or could not be updated'
            ], 404);
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or password could not be updated'
            ], 404);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'User activated successfully' : 'User deactivated successfully',
                'data' => ['is_active' => $user->is_active]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Delete a user (soft delete by deactivating)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Check if user has active bookings
            $activeBookings = $user->bookings()
                ->whereIn('status', ['pending', 'approved'])
                ->where('scheduled_date', '>=', now()->toDateString())
                ->count();

            if ($activeBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user with active bookings'
                ], 422);
            }

            // Soft delete by deactivating
            $user->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Get reference data for user creation/editing
     */
    public function getReferenceData(): JsonResponse
    {
        try {
            $data = [
                'roles' => Role::all(),
                'business_units' => BusinessUnit::all(),
                'company_codes' => CompanyCode::all(),
                'branches' => Branch::all(),
                'departments' => Department::all(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching reference data'
            ], 500);
        }
    }

    /**
     * Bulk update user status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'is_active' => 'required|boolean',
            ]);

            $updatedCount = User::whereIn('id', $validated['user_ids'])
                ->update(['is_active' => $validated['is_active']]);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} users updated successfully"
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating users'
            ], 500);
        }
    }
}