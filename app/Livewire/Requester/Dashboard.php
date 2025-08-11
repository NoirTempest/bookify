<?php

namespace App\Livewire\Requester;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $statistics = [];
    public $recentBookings = [];
    public $upcomingBookings = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $userId = Auth::id();

        // Get booking statistics for the current user
        $totalBookings = Booking::where('user_id', $userId)->count();
        $pendingBookings = Booking::where('user_id', $userId)->where('status', 'pending')->count();
        $approvedBookings = Booking::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedBookings = Booking::where('user_id', $userId)->where('status', 'rejected')->count();
        $cancelledBookings = Booking::where('user_id', $userId)->where('status', 'cancelled')->count();

        $this->statistics = [
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'approved_bookings' => $approvedBookings,
            'rejected_bookings' => $rejectedBookings,
            'cancelled_bookings' => $cancelledBookings,
        ];

        // Get recent bookings (last 5)
        $this->recentBookings = Booking::with(['assetType', 'assetDetail'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming bookings
        $this->upcomingBookings = Booking::with(['assetType', 'assetDetail'])
            ->where('user_id', $userId)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->where('status', 'approved')
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('time_from', 'asc')
            ->limit(5)
            ->get();
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        return view('livewire.requester.dashboard');
    }
}