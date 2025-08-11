<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Branch;

class BranchManagement extends Component
{
    use WithPagination;

    public $perPage = 6;
    public $branchPage = 1;

    public $branchName = '';
    public $editingId = null;
    public $editMode = false;
    public $showForm = false;

    protected $queryString = [
        'branchPage' => ['except' => 1],
    ];

    protected $rules = [
        'branchName' => 'required|string|max:255',
    ];

    public function updatingBranchPage()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.branch-management', [
            'branches' => Branch::paginate($this->perPage, ['*'], 'branchPage'),
        ]);
    }

    public function store()
    {
        $this->validate();

        Branch::create([
            'name' => $this->branchName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Branch added successfully.');


        $this->resetForm();
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        $this->editingId = $branch->id;
        $this->branchName = $branch->name;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $branch = Branch::findOrFail($this->editingId);
        $branch->update([
            'name' => $this->branchName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Branch updated successfully.');


        $this->resetForm();
    }

    public function delete($id)
    {
        Branch::find($id)?->delete();

        $this->dispatch('notify', type: 'success', message: 'Branch deleted successfully.');

    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['branchName', 'editingId', 'editMode', 'showForm']);
    }
}
