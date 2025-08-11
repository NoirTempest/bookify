<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('users')->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    public function create(): JsonResponse
    {
        $availableRoles = ['Admin', 'Manager', 'User', 'Driver'];

        return response()->json([
            'success' => true,
            'data' => [
                'available_roles' => $availableRoles,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', Rule::in(['Admin', 'Manager', 'User', 'Driver']), 'unique:roles,name'],
            ]);

            $role = Role::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('users');

        return response()->json([
            'success' => true,
            'data' => $role,
        ]);
    }

    public function edit(Role $role): JsonResponse
    {
        $availableRoles = ['Admin', 'Manager', 'User', 'Driver'];

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role,
                'available_roles' => $availableRoles,
            ],
        ]);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', Rule::in(['Admin', 'Manager', 'User', 'Driver']), 'unique:roles,name,' . $role->id],
            ]);

            $role->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role with associated users',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }
}
