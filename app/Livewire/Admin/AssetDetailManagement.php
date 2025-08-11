<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AssetDetail;
use App\Models\AssetType;

class AssetDetailManagement extends Component
{
    use WithPagination;

    public $perPage = 6;
    public $assetPage = 1;

    public $search = ''; // ✅ Search keyword

    public $assetTypeId;
    public $assetName;
    public $location;
    public $brand;
    public $model;
    public $color;
    public $plateNumber;
    public $numberOfSeats;

    public $editingId = null;
    public $editMode = false;
    public $showForm = false;

    protected $queryString = [
        'assetPage' => ['except' => 1],
        'search' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'assetTypeId' => 'required|exists:asset_types,id',
            'assetName' => 'required|string|max:255',
            'location' => 'nullable|string',
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'color' => 'nullable|string',
            'plateNumber' => 'nullable|string',
            'numberOfSeats' => 'nullable|integer',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage(); // ✅ Reset pagination on search update
    }

    public function updatingAssetPage()
    {
        $this->resetForm();
    }

    public function render()
    {
        $query = AssetDetail::with('assetType')
            ->when($this->search, function ($q) {
                $q->where('asset_name', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%');
            });

        return view('livewire.admin.asset-detail-management', [
            'assetDetails' => $query->paginate($this->perPage),
            'assetTypes' => AssetType::all(),
        ]);
    }

    public function startAdd($typeId)
    {
        $this->resetForm();
        $this->assetTypeId = (int) $typeId;
        $this->showForm = true;
    }

    public function store()
    {
        $this->validate([
            'assetTypeId' => 'required|integer',
            'assetName' => 'required|string|max:255',
            'location' => $this->assetTypeId == 1 ? 'required|string|max:255' : 'nullable|string|max:255',
            'numberOfSeats' => 'required|integer|min:1',
            'brand' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
            'model' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
            'color' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
            'plateNumber' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
        ]);

        AssetDetail::create([
            'asset_type_id' => $this->assetTypeId,
            'asset_name' => $this->assetName,
            'location' => $this->assetTypeId == 1 ? $this->location : null,
            'brand' => $this->assetTypeId == 2 ? $this->brand : null,
            'model' => $this->assetTypeId == 2 ? $this->model : null,
            'color' => $this->assetTypeId == 2 ? $this->color : null,
            'plate_number' => $this->assetTypeId == 2 ? $this->plateNumber : null,
            'number_of_seats' => $this->numberOfSeats,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Asset added successfully.');
        $this->resetForm();
    }



    public function edit($id)
    {
        $asset = AssetDetail::findOrFail($id);

        $this->editingId = $asset->id;
        $this->assetTypeId = $asset->asset_type_id;
        $this->assetName = $asset->asset_name;
        $this->location = $asset->location;
        $this->brand = $asset->brand;
        $this->model = $asset->model;
        $this->color = $asset->color;
        $this->plateNumber = $asset->plate_number;
        $this->numberOfSeats = $asset->number_of_seats;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate([
            'assetTypeId' => 'required|integer',
            'assetName' => 'required|string|max:255',
            'location' => $this->assetTypeId == 1 ? 'required|string|max:255' : 'nullable|string|max:255',
            'numberOfSeats' => 'required|integer|min:1',
            'brand' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
            'model' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
            'color' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
            'plateNumber' => $this->assetTypeId == 2 ? 'required|string|max:255' : 'nullable',
        ]);

        $asset = AssetDetail::findOrFail($this->editingId);
        $asset->update([
            'asset_type_id' => $this->assetTypeId,
            'asset_name' => $this->assetName,
            'location' => $this->assetTypeId == 1 ? $this->location : null,
            'brand' => $this->assetTypeId == 2 ? $this->brand : null,
            'model' => $this->assetTypeId == 2 ? $this->model : null,
            'color' => $this->assetTypeId == 2 ? $this->color : null,
            'plate_number' => $this->assetTypeId == 2 ? $this->plateNumber : null,
            'number_of_seats' => $this->numberOfSeats,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Asset updated successfully.');
        $this->resetForm();
    }


    public function delete($id)
    {
        AssetDetail::find($id)?->delete();
        $this->dispatch('notify', type: 'success', message: 'Asset deleted successfully');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'assetTypeId',
            'assetName',
            'location',
            'brand',
            'model',
            'color',
            'plateNumber',
            'numberOfSeats',
            'editingId',
            'editMode',
            'showForm'
        ]);
    }
}
