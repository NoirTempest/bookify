<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class UserTables extends Component
{
    use WithPagination;

    public $perPage = 10;

    public function render()
    {
        $roles = Role::pluck('id', 'name')->toArray();

        return view('livewire.admin.user-tables', [
            'admins' => isset($roles['Admin']) ? User::where('role_id', $roles['Admin'])->paginate($this->perPage, ['*'], 'admins') : collect(),
            'approvers' => isset($roles['Approver']) ? User::where('role_id', $roles['Approver'])->paginate($this->perPage, ['*'], 'approvers') : collect(),
            'users' => isset($roles['User']) ? User::where('role_id', $roles['User'])->paginate($this->perPage, ['*'], 'users') : collect(),
            'drivers' => isset($roles['Driver']) ? User::where('role_id', $roles['Driver'])->paginate($this->perPage, ['*'], 'drivers') : collect(),
            'adminStaffs' => isset($roles['Admin Staff']) ? User::where('role_id', $roles['Admin Staff'])->paginate($this->perPage, ['*'], 'admin_staffs') : collect(),
        ]);
    }

}
