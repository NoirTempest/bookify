<?php 

namespace App\Livewire\Requester\Booking;

use Livewire\Component;
use App\Models\Booking;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ViewBooking extends Component
{
    public $booking;

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load('assetType', 'assetDetail');
    }

    public function render()
    {
        return view('livewire.requester.booking.view-booking');
    }
}
