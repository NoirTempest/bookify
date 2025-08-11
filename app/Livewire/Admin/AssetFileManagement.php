<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\AssetDetail;
use App\Models\AssetFile;

class AssetFileManagement extends Component
{
    use WithFileUploads, WithPagination;

    public $assetDetailId;
    public $fileAttachment;
    public $showForm = false;
    protected string $paginationTheme = 'bootstrap';

    protected $rules = [
        'assetDetailId' => 'required|exists:asset_details,id',
        'fileAttachment' => 'required|image|max:5120', // 5MB in KB
    ];

    protected $messages = [
        'assetDetailId.required' => 'Please select an asset.',
        'fileAttachment.required' => 'Please upload an image.',
        'fileAttachment.image' => 'The file must be an image.',
        'fileAttachment.max' => 'Image must not exceed 5MB.',
    ];

    public function updatedFileAttachment()
    {
        $this->validateOnly('fileAttachment');
    }

    public function save()
    {
        $this->validate();

        $originalName = $this->fileAttachment->getClientOriginalName();
        $safeName = time() . '_' . preg_replace('/\s+/', '_', $originalName);
        $path = $this->fileAttachment->storeAs('asset_images', $safeName, 'public');

        AssetFile::create([
            'asset_detail_id' => $this->assetDetailId,
            'file_attachments' => $path,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Asset uploaded successfully.');
        $this->reset(['fileAttachment', 'assetDetailId', 'showForm']);
        $this->resetPage();
    }

    public function deleteFile($id)
    {
        $file = AssetFile::find($id);
        if ($file) {
            $filePath = storage_path('app/public/' . $file->file_attachments);
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
            $file->delete();
            $this->dispatch('notify', type: 'success', message: 'Asset deleted successfully.');
        }
    }

    public function cancel()
    {
        $this->reset(['assetDetailId', 'fileAttachment', 'showForm']);
    }

    public function render()
    {
        $assets = AssetDetail::with('files')->paginate(5);
        return view('livewire.admin.asset-file-management', compact('assets'));
    }
}
