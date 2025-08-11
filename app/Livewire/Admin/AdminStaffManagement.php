<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Department;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class AdminStaffManagement extends Component
{
    use WithPagination;

    public $first_name, $last_name, $email, $password, $mobile_number, $userId;
    public $branch_id, $department_id, $business_unit_id, $company_code_id;
    public $editMode = false;
    public $showForm = false;
    public $perPage = 10;

    public $branches, $departments, $businessUnits, $companyCodes;

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->editMode ? ',' . $this->userId : ''),
            'mobile_number' => 'required|string|max:20',
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

    public function mount()
    {
        $this->branches = Branch::all();
        $this->departments = Department::all();
        $this->businessUnits = BusinessUnit::all();
        $this->companyCodes = CompanyCode::all();
    }

    public function render()
    {
        $adminStaffRoleId = Role::where('name', 'Admin Staff')->value('id');
        $users = User::where('role_id', $adminStaffRoleId)->latest()->paginate($this->perPage);

        return view('livewire.admin.admin-staff-management', compact('users'));
    }

    public function save()
    {
        $this->validate();

        $adminStaffRoleId = Role::where('name', 'Admin Staff')->value('id');

        User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'password' => Hash::make($this->password),
            'role_id' => $adminStaffRoleId,
            'branch_id' => $this->branch_id,
            'department_id' => $this->department_id,
            'business_unit_id' => $this->business_unit_id,
            'company_code_id' => $this->company_code_id,
            'is_active' => true,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Admin Staff added successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
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
        $this->validate();

        $user = User::findOrFail($this->userId);
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

        $this->dispatch('notify', type: 'success', message: 'Admin Staff updated successfully.');
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            User::findOrFail($id)->delete();
            $this->dispatch('notify', type: 'success', message: 'Admin staff deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('notify', type: 'error', message: 'Cannot delete admin staff because they have related bookings.');
        }
    }


    public function resetForm()
    {
        $this->reset([
            'first_name',
            'last_name',
            'email',
            'password',
            'mobile_number',
            'userId',
            'editMode',
            'showForm',
            'branch_id',
            'department_id',
            'business_unit_id',
            'company_code_id'
        ]);
        $this->resetValidation();
    }

    private function dispatchNotify($message)
    {
        $escapedMessage = addslashes($message);
        $this->js(<<<JS
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: "{$escapedMessage}" }
            }));
        JS);
    }
}
