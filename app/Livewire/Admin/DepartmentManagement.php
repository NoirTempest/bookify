<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;

class DepartmentManagement extends Component
{
    use WithPagination;

    public $perPage = 6;
    public $departmentPage = 1;

    public $departmentName = '';
    public $editingId = null;
    public $editMode = false;
    public $showForm = false;

    protected $queryString = [
        'departmentPage' => ['except' => 1],
    ];

    protected $rules = [
        'departmentName' => 'required|string|max:255',
    ];

    public function updatingDepartmentPage()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.department-management', [
            'departments' => Department::paginate($this->perPage, ['*'], 'departmentPage'),
        ]);
    }

    public function store()
    {
        $this->validate();

        Department::create([
            'name' => $this->departmentName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Department added successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->editingId = $department->id;
        $this->departmentName = $department->name;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        Department::findOrFail($this->editingId)->update([
            'name' => $this->departmentName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Department updated successfully.');
        $this->resetForm();
    }

    public function delete($id)
    {
        Department::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Department deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['departmentName', 'editingId', 'editMode', 'showForm']);
    }

}
