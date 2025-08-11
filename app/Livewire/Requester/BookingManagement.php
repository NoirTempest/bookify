<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\Booking;

class BookingManagement extends Component
{
    public function render()
    {
        $bookings = Booking::with(['user', 'assetType'])->latest()->get();

        return view('livewire.requester.bookings', [ // <- use bookings.blade.php
            'bookings' => $bookings,
        ]);
    }
}

