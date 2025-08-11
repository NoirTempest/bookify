<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Department;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;

class ApproverManagement extends Component
{
    use WithPagination;

    public $first_name, $last_name, $email, $mobile_number, $password, $approver_id;
    public $branch_id, $department_id, $business_unit_id, $company_code_id;
    public $editMode = false, $showForm = false;

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->editMode ? ',' . $this->approver_id : ''),
            'mobile_number' => 'required|numeric|digits_between:9,15',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'business_unit_id' => 'required|exists:business_units,id',
            'company_code_id' => 'required|exists:company_codes,id',
        ];

        if (!$this->editMode) {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    public function render()
    {
        $approverRoleId = Role::where('name', 'Approver')->value('id');
        $approvers = User::where('role_id', $approverRoleId)->paginate(10);

        $branches = Branch::all();
        $departments = Department::all();
        $businessUnits = BusinessUnit::all();
        $companyCodes = CompanyCode::all();

        return view('livewire.admin.approver-management', compact(
            'approvers',
            'branches',
            'departments',
            'businessUnits',
            'companyCodes'
        ));
    }

    public function save()
    {
        $this->validate();

        $approverRoleId = Role::where('name', 'Approver')->value('id');

        User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'mobile_number' => $this->mobile_number,
            'role_id' => $approverRoleId,
            'branch_id' => $this->branch_id,
            'department_id' => $this->department_id,
            'business_unit_id' => $this->business_unit_id,
            'company_code_id' => $this->company_code_id,
            'is_active' => true,
        ]);

        $this->resetForm();
        $this->dispatch('notify', type: 'success', message: 'Approver added successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->approver_id = $user->id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->mobile_number = $user->mobile_number;
        $this->branch_id = $user->branch_id;
        $this->department_id = $user->department_id;
        $this->business_unit_id = $user->business_unit_id;
        $this->company_code_id = $user->company_code_id;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function update()
    {
        $user = User::findOrFail($this->approver_id);
        $this->validate();

        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'branch_id' => $this->branch_id,
            'department_id' => $this->department_id,
            'business_unit_id' => $this->business_unit_id,
            'company_code_id' => $this->company_code_id,
        ]);

        $this->resetForm();
        $this->dispatch('notify', type: 'success', message: 'Approver updated successfully.');
    }

    public function delete($id)
    {
        try {
            User::findOrFail($id)->delete();
            $this->dispatch('notify', type: 'success', message: 'Approver deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete approver because they have related bookings.');
        }
    }


    public function resetForm()
    {
        $this->reset([
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'password',
            'approver_id',
            'branch_id',
            'department_id',
            'business_unit_id',
            'company_code_id',
            'editMode',
            'showForm'
        ]);
    }
}
