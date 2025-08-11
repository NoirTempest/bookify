<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    public function index(): JsonResponse
    {
        $branches = Branch::with('users')->get();

        return response()->json([
            'success' => true,
            'data' => $branches,
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
                'name' => 'required|string|max:255|unique:branches,name',
            ]);

            $branch = Branch::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Branch created successfully',
                'data' => $branch,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(Branch $branch): JsonResponse
    {
        $branch->load('users');

        return response()->json([
            'success' => true,
            'data' => $branch,
        ]);
    }

    public function edit(Branch $branch): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $branch,
        ]);
    }

    public function update(Request $request, Branch $branch): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            ]);

            $branch->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully',
                'data' => $branch,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Branch $branch): JsonResponse
    {
        if ($branch->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete branch with associated users',
            ], 422);
        }

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully',
        ]);
    }
}
