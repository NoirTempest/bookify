<?php 

namespace App\Livewire\Requester\Booking;

use Livewire\Component;
use App\Models\Booking;

class EditModal extends Component
{
    public $booking, $status;

    protected $listeners = ['openEditModal' => 'loadBooking'];

    public function loadBooking($id)
    {
        $this->booking = Booking::findOrFail($id);
        $this->status = $this->booking->status;
        $this->dispatch('show-edit-modal');
    }

    public function update()
    {
        $this->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $this->booking->status = $this->status;
        $this->booking->save();

        $this->dispatch('hide-edit-modal');
        session()->flash('message', 'Booking updated successfully.');
    }

    public function render()
    {
        return view('livewire.requester.booking.edit-modal')
            ->layout('layouts.app');
    }
}
