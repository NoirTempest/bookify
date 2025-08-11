<?php

namespace App\Http\Controllers;

use App\Models\AssetFile;
use App\Models\AssetDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class AssetFileController extends Controller
{
    public function index(): JsonResponse
    {
        $assetFiles = AssetFile::with('assetDetail.assetType')->get();

        return response()->json([
            'success' => true,
            'data' => $assetFiles,
        ]);
    }

    public function create(): JsonResponse
    {
        $assetDetails = AssetDetail::with('assetType')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'asset_details' => $assetDetails,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_detail_id' => 'required|exists:asset_details,id',
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240', // 10MB max
            ]);

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('asset_files', $fileName, 'public');

                $assetFile = AssetFile::create([
                    'asset_detail_id' => $validated['asset_detail_id'],
                    'file_attachments' => $filePath,
                ]);

                $assetFile->load('assetDetail.assetType');

                return response()->json([
                    'success' => true,
                    'message' => 'Asset file uploaded successfully',
                    'data' => $assetFile,
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file was uploaded',
            ], 422);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(AssetFile $assetFile): JsonResponse
    {
        $assetFile->load('assetDetail.assetType');

        return response()->json([
            'success' => true,
            'data' => $assetFile,
        ]);
    }

    public function edit(AssetFile $assetFile): JsonResponse
    {
        $assetDetails = AssetDetail::with('assetType')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'asset_file' => $assetFile->load('assetDetail'),
                'asset_details' => $assetDetails,
            ],
        ]);
    }

    public function update(Request $request, AssetFile $assetFile): JsonResponse
    {
        try {
            $validated = $request->validate([
                'asset_detail_id' => 'required|exists:asset_details,id',
                'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240', // 10MB max
            ]);

            $updateData = [
                'asset_detail_id' => $validated['asset_detail_id'],
            ];

            // Handle file upload if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($assetFile->file_attachments && Storage::disk('public')->exists($assetFile->file_attachments)) {
                    Storage::disk('public')->delete($assetFile->file_attachments);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('asset_files', $fileName, 'public');

                $updateData['file_attachments'] = $filePath;
            }

            $assetFile->update($updateData);
            $assetFile->load('assetDetail.assetType');

            return response()->json([
                'success' => true,
                'message' => 'Asset file updated successfully',
                'data' => $assetFile,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(AssetFile $assetFile): JsonResponse
    {
        // Delete file from storage
        if ($assetFile->file_attachments && Storage::disk('public')->exists($assetFile->file_attachments)) {
            Storage::disk('public')->delete($assetFile->file_attachments);
        }

        $assetFile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset file deleted successfully',
        ]);
    }

    /**
     * Download asset file
     */
    public function download(AssetFile $assetFile)
    {
        if (!$assetFile->file_attachments || !Storage::disk('public')->exists($assetFile->file_attachments)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        return Storage::disk('public')->download($assetFile->file_attachments);
    }

    /**
     * Get files for a specific asset detail
     */
    public function getFilesByAssetDetail(AssetDetail $assetDetail): JsonResponse
    {
        $files = $assetDetail->assetFiles;

        return response()->json([
            'success' => true,
            'data' => $files,
        ]);
    }

    /**
     * Upload multiple files for an asset detail
     */
    public function uploadMultipleFiles(Request $request, AssetDetail $assetDetail): JsonResponse
    {
        try {
            $validated = $request->validate([
                'files' => 'required|array|min:1|max:10',
                'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            ]);

            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('asset_files', $fileName, 'public');

                $assetFile = AssetFile::create([
                    'asset_detail_id' => $assetDetail->id,
                    'file_attachments' => $filePath,
                ]);

                $uploadedFiles[] = $assetFile;
            }

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'data' => $uploadedFiles,
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
     * Get file info without downloading
     */
    public function getFileInfo(AssetFile $assetFile): JsonResponse
    {
        $fileExists = $assetFile->file_attachments && Storage::disk('public')->exists($assetFile->file_attachments);
        $fileSize = $fileExists ? Storage::disk('public')->size($assetFile->file_attachments) : 0;
        $fileUrl = $fileExists ? Storage::disk('public')->url($assetFile->file_attachments) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'asset_file' => $assetFile,
                'file_exists' => $fileExists,
                'file_size' => $fileSize,
                'file_url' => $fileUrl,
                'file_size_human' => $fileExists ? $this->formatBytes($fileSize) : '0 B',
            ],
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
