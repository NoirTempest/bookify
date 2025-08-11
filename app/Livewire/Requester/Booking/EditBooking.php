<?php

namespace App\Livewire\Requester\Booking;


use Livewire\Component;
use App\Models\Booking;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EditBooking extends Component
{
    public $booking;
    public $status;

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
        $this->status = $booking->status;
    }

    public function update()
    {
        $this->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $this->booking->update(['status' => $this->status]);

        session()->flash('message', 'Booking updated.');
        return redirect()->route('bookings.view', $this->booking->id);
    }

    public function render()
    {
        return view('livewire.requester.booking.edit-booking');
    }
}
