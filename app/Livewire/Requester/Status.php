<?php
namespace App\Livewire\Requester;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Booking;
use Carbon\Carbon;

#[Layout('layouts.app')]
class Status extends Component
{
    public $currentBookings = [];
    public $upcomingBookings = [];

    public function mount()
    {
        $this->loadRoomStatus();
    }

    public function loadRoomStatus()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // Get current bookings (happening right now)
        $this->currentBookings = Booking::with(['assetDetail', 'user'])
            ->where('scheduled_date', $today)
            ->where('status', 'approved')
            ->where('time_from', '<=', $now->format('H:i'))
            ->where('time_to', '>=', $now->format('H:i'))
            ->get();

        // Get upcoming bookings for today
        $this->upcomingBookings = Booking::with(['assetDetail', 'user'])
            ->where('scheduled_date', $today)
            ->where('status', 'approved')
            ->where('time_from', '>', $now->format('H:i'))
            ->orderBy('time_from')
            ->limit(10)
            ->get();
    }

    public function refresh()
    {
        $this->loadRoomStatus();
    }

    public function render()
    {
        return view('livewire.requester.status');
    }
}
