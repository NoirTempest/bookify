<?php

namespace App\Livewire\Requester\Booking;

use Livewire\Component;
use App\Models\Booking;

class ViewModal extends Component
{
    public $booking;

    protected $listeners = ['openViewModal' => 'loadBooking'];

    public function loadBooking($id)
    {
        $this->booking = Booking::with(['assetType', 'assetDetail'])->findOrFail($id);
        $this->dispatch('show-view-modal');
    }

    public function render()
    {
        return view('livewire.requester.booking.view-modal')
            ->layout('layouts.app');
    }
}

