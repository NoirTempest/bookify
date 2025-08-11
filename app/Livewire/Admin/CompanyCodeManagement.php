<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CompanyCode;

class CompanyCodeManagement extends Component
{
    use WithPagination;

    public $perPage = 6;
    public $companyCodePage = 1;

    public $companyCodeName = '';
    public $editingId = null;
    public $editMode = false;
    public $showForm = false;

    protected $queryString = [
        'companyCodePage' => ['except' => 1],
    ];

    protected $rules = [
        'companyCodeName' => 'required|string|max:255',
    ];

    public function updatingCompanyCodePage()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.company-code-management', [
            'companyCodes' => CompanyCode::paginate($this->perPage, ['*'], 'companyCodePage'),
        ]);
    }

    public function store()
    {
        $this->validate();

        CompanyCode::create([
            'name' => $this->companyCodeName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Company Code successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $companyCode = CompanyCode::findOrFail($id);
        $this->editingId = $companyCode->id;
        $this->companyCodeName = $companyCode->name;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $companyCode = CompanyCode::findOrFail($this->editingId);
        $companyCode->update([
            'name' => $this->companyCodeName,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Company Code updated successfully.');
        $this->resetForm();
    }

    public function delete($id)
    {
        CompanyCode::find($id)?->delete();
        $this->dispatch('notify', type: 'success', message: 'Company Code deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['companyCodeName', 'editingId', 'editMode', 'showForm']);
    }
}
