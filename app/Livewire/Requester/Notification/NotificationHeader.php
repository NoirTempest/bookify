<?php

namespace App\Livewire\Requester\Notification;

use App\Models\Booking;
use Carbon\Carbon;
use Livewire\Component;

class NotificationHeader extends Component
{
    public $bookings;

    public function mount()
    {
        // Set PH timezone
        $now = Carbon::now('Asia/Manila');

        $this->bookings = Booking::with('assetType')
            ->whereHas('assetType', function ($query) {
                $query->whereIn('name', ['Meeting Room', 'Conference Room']);
            })
            ->where('status', 'approved')
            ->whereDate('scheduled_date', $now->toDateString())
            ->whereTime('time_from', '<=', $now->format('H:i:s'))
            ->whereTime('time_to', '>=', $now->format('H:i:s'))
            ->get();
    }

    public function render()
    {
        return view('livewire.requester.notification.notification-header');
    }
}
