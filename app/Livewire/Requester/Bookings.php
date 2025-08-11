<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\Booking;
use App\Models\AssetType;
use App\Models\AssetDetail;
use Livewire\WithPagination;

class Bookings extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search = '';
    public $assetTypes;
    public $assetDetails;

    protected string $paginationTheme = 'bootstrap';

    public $deleteId = null;

    public function mount()
    {
        $this->assetTypes = AssetType::all();
        $this->assetDetails = AssetDetail::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('confirm-delete'); // Livewire v3-style event
    }

    public function deleteBooking($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($booking) {
            // Delete related booked guests
            $booking->bookedGuests()->delete();

            // Delete the booking
            $booking->delete();

            session()->flash('message', 'Booking and related guests deleted successfully.');
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Booking::with(['user', 'assetType'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('assetType', fn($q) => $q->where('name', 'like', $searchTerm))
                    ->orWhereHas('user', fn($q) => $q->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm))
                    ->orWhere('destination', 'like', $searchTerm)
                    ->orWhere('status', 'like', $searchTerm);
            });
        }

        return view('livewire.requester.bookings', [
            'bookings' => $query->paginate(7),
            'assetTypes' => $this->assetTypes,
            'assetDetails' => $this->assetDetails,
        ])->layout('layouts.app');
    }
}
