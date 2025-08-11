<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Branch;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;

class OrganizationManagement extends Component
{
    public function render()
    {
        return view('livewire.admin.organization-management', [
            'branches' => Branch::all(),
            'businessUnits' => BusinessUnit::all(),
            'companyCodes' => CompanyCode::all(),
        ])->layout('layouts.admin');
    }
}
