<?php

namespace App\Livewire\AdminStaff;

use Livewire\Component;
use App\Models\Booking;
use App\Models\User;
use App\Models\Driver;
use App\Models\AssetDetail;
class Dashboard extends Component
{

    public $stats = [];

    public function mount()
    {
        $this->stats = [
            'Total Bookings' => Booking::count(),
            'Approved Bookings' => Booking::where('status', 'approved')->count(),
            'Pending Bookings' => Booking::where('status', 'pending')->count(),
            'Rejected Bookings' => Booking::where('status', 'rejected')->count(),
            'Ended Bookings' => Booking::where('status', 'ended')->count(),
            'Upcoming Bookings' => Booking::whereDate('scheduled_date', '>', now())->count(),
            'Total Users' => User::count(),
            'Total Drivers' => Driver::count(),
            'Total Assets' => AssetDetail::count(),
        ];
    }
    public function render()
    {
        return view('livewire.admin-staff.dashboard')
            ->layout('layouts.adminstaff'); // ✅ Use correct layout
    }

}





