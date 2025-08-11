<?php

namespace App\Http\Controllers;

use App\Models\CompanyCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompanyCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $companyCodes = CompanyCode::with('users')->get();

        return response()->json([
            'success' => true,
            'data' => $companyCodes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
                'name' => 'required|string|max:255|unique:company_codes,name',
            ]);

            $companyCode = CompanyCode::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Company code created successfully',
                'data' => $companyCode,
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
    public function show(CompanyCode $companyCode): JsonResponse
    {
        $companyCode->load('users');

        return response()->json([
            'success' => true,
            'data' => $companyCode,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanyCode $companyCode): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $companyCode,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyCode $companyCode): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:company_codes,name,' . $companyCode->id,
            ]);

            $companyCode->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Company code updated successfully',
                'data' => $companyCode,
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
    public function destroy(CompanyCode $companyCode): JsonResponse
    {
        if ($companyCode->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete company code with associated users',
            ], 422);
        }

        $companyCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company code deleted successfully',
        ]);
    }
}
