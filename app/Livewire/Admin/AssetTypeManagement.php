<?php 

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AssetType;

class AssetTypeManagement extends Component
{
    use WithPagination;
    public $perPage = 6, $assetTypeName = '', $editingId = null, $editMode = false, $showForm = false;

    protected $rules = ['assetTypeName' => 'required|string|max:255'];
    
    protected $queryString = ['page' => ['except' => 1]];

    public function updatingPage() { $this->resetForm(); }

    public function render()
    {
        return view('livewire.admin.asset-type-management', [
            'assetTypes' => AssetType::paginate($this->perPage)
        ]);
    }

    public function store()
    {
        $this->validate();
        AssetType::create(['name' => $this->assetTypeName]);
        $this->dispatch('notify', type: 'success', message: 'Asset added successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $type = AssetType::findOrFail($id);
        $this->editingId = $id; $this->assetTypeName = $type->name;
        $this->editMode = true; $this->showForm = true;
    }

    public function update()
    {
        $this->validate();
        AssetType::findOrFail($this->editingId)->update(['name' => $this->assetTypeName]);
        $this->dispatch('notify', type: 'success', message: 'Asset updated successfully.');
        $this->resetForm();
    }

    public function delete($id)
    {
        AssetType::find($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Asset deleted successfully.');
    }

    public function cancel() { $this->resetForm(); }

    public function resetForm()
    {
        $this->reset(['assetTypeName', 'editingId', 'editMode', 'showForm']);
    }
}
