<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessUnit;

class BusinessUnitManagement extends Component
{
    use WithPagination;

    public $perPage = 6;
    public $businessUnitPage = 1;

    public $businessUnitName = '';
    public $editingId = null;
    public $editMode = false;
    public $showForm = false;

    protected $queryString = [
        'businessUnitPage' => ['except' => 1],
    ];

    protected $rules = [
        'businessUnitName' => 'required|string|max:255',
    ];

    public function updatingBusinessUnitPage()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.business-unit-management', [
            'businessUnits' => BusinessUnit::paginate($this->perPage, ['*'], 'businessUnitPage'),
        ]);
    }

    public function store()
    {
        $this->validate();

        BusinessUnit::create([
            'name' => $this->businessUnitName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Business Unit added successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $unit = BusinessUnit::findOrFail($id);
        $this->editingId = $unit->id;
        $this->businessUnitName = $unit->name;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $unit = BusinessUnit::findOrFail($this->editingId);
        $unit->update([
            'name' => $this->businessUnitName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Business Unit updated successfully.');
        $this->resetForm();
    }

    public function delete($id)
    {
        BusinessUnit::find($id)?->delete();
        $this->dispatch('notify', type: 'success', message: 'Business Unit deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['businessUnitName', 'editingId', 'editMode', 'showForm']);
    }
}
