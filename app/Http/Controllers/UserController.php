<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with([
            'branch',
            'department',
            'businessUnit',
            'companyCode',
            'role'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function create(): JsonResponse
    {
        $branches = Branch::all();
        $departments = Department::all();
        $businessUnits = BusinessUnit::all();
        $companyCodes = CompanyCode::all();
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => [
                'branches' => $branches,
                'departments' => $departments,
                'business_units' => $businessUnits,
                'company_codes' => $companyCodes,
                'roles' => $roles,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,id',
                'department_id' => 'required|exists:departments,id',
                'business_unit_id' => 'required|exists:business_units,id',
                'company_code_id' => 'required|exists:company_codes,id',
                'role_id' => 'required|exists:roles,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:15|unique:users,mobile_number',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'is_active' => 'boolean',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $validated['is_active'] ?? true;

            $user = User::create($validated);
            $user->load(['branch', 'department', 'businessUnit', 'companyCode', 'role']);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(User $user): JsonResponse
    {
        $user->load([
            'branch',
            'department',
            'businessUnit',
            'companyCode',
            'role',
            'bookings.assetDetail',
            'approvers.assetType'
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function edit(User $user): JsonResponse
    {
        $branches = Branch::all();
        $departments = Department::all();
        $businessUnits = BusinessUnit::all();
        $companyCodes = CompanyCode::all();
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load(['branch', 'department', 'businessUnit', 'companyCode', 'role']),
                'branches' => $branches,
                'departments' => $departments,
                'business_units' => $businessUnits,
                'company_codes' => $companyCodes,
                'roles' => $roles,
            ],
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,id',
                'department_id' => 'required|exists:departments,id',
                'business_unit_id' => 'required|exists:business_units,id',
                'company_code_id' => 'required|exists:company_codes,id',
                'role_id' => 'required|exists:roles,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:15|unique:users,mobile_number,' . $user->id,
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'is_active' => 'boolean',
            ]);

            // Only update password if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);
            $user->load(['branch', 'department', 'businessUnit', 'companyCode', 'role']);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        // Check if user has bookings
        if ($user->bookings()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with associated bookings',
            ], 422);
        }

        // Check if user is an approver
        if ($user->approvers()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user who is set as an approver',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                ], 422);
            }

            $user->update(['password' => Hash::make($validated['new_password'])]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
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
