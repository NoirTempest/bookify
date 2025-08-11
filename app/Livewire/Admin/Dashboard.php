<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\AssetFile;
use App\Models\AssetType;
use App\Models\AssetDetail;
use App\Models\Driver;
use App\Models\VehicleDriverAssignment;
use App\Models\BusinessUnit;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'assetTypes' => AssetType::count(),
            'assetFiles' => AssetFile::count(),
            'assetDetails' => AssetDetail::count(),
            'drivers' => Driver::count(),
            'assignments' => VehicleDriverAssignment::count(),
            'businessUnits' => BusinessUnit::count(),
            'branches' => Branch::count(),
            'departments' => Department::count(),
            'roles' => Role::count(),

            'recentBookings' => Booking::with([
                'user.department',
                'user.branch',
                'assetDetail.assetType',
            ])
                ->whereIn('asset_type_id', [1, 2])
                ->latest()
                ->get(),
        ]);
    }
}
