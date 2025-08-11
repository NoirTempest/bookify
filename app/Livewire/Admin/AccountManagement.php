<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class AccountManagement extends Component
{
    use WithPagination;

    public $perPage = 10;

    // Page tracking for each tab
    public $accountPage = 1;
    public $adminStaffPage = 1;
    public $approverPage = 1;
    public $userPage = 1;

    protected $queryString = [
        'accountPage' => ['except' => 1],
        'adminStaffPage' => ['except' => 1],
        'approverPage' => ['except' => 1],
        'userPage' => ['except' => 1],
    ];

    public function render()
    {
        return view('livewire.admin.account-management', [
            'allUsers' => User::with('role')
                ->paginate($this->perPage, ['*'], 'accountPage'),

            'adminStaffs' => User::with('role')
                ->whereHas('role', fn($q) => $q->where('name', 'Admin Staff'))
                ->paginate($this->perPage, ['*'], 'adminStaffPage'),

            // 🔥 Remove 'drivers' from here; loaded in DriverManagement component

            'approvers' => User::with('role')
                ->whereHas('role', fn($q) => $q->where('name', 'Approver'))
                ->paginate($this->perPage, ['*'], 'approverPage'),

            'users' => User::with('role')
                ->whereHas('role', fn($q) => $q->where('name', 'User'))
                ->paginate($this->perPage, ['*'], 'userPage'),
        ])->layout('layouts.admin');
    }
}
