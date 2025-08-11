<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingsList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'scheduled_date';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortBy' => ['except' => 'scheduled_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($booking) {
            $booking->update(['status' => 'cancelled']);
            session()->flash('message', 'Booking cancelled successfully.');
        } else {
            session()->flash('error', 'Booking not found or cannot be cancelled.');
        }
    }

    public function render()
    {
        $query = Booking::with(['assetType', 'assetDetail', 'bookedGuests'])
            ->where('user_id', Auth::id());

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('purpose', 'like', '%' . $this->search . '%')
                    ->orWhere('destination', 'like', '%' . $this->search . '%')
                    ->orWhereHas('assetType', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('assetDetail', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply date filters
        if ($this->dateFrom) {
            $query->where('scheduled_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('scheduled_date', '<=', $this->dateTo);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $bookings = $query->paginate(15);

        return view('livewire.requester.bookings-list', [
            'bookings' => $bookings
        ])->layout('layouts.requester');
    }
}