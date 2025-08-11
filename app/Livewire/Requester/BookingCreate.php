<?php

namespace App\Livewire\Requester;

use App\Models\AssetType;
use App\Models\AssetDetail;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.requester')]
class BookingCreate extends Component
{
    public $assetTypes = [];
    public $assetDetails = [];
    public $selectedAssetType = null;
    public $selectedAssetDetail = null;
    public $scheduledDate = '';
    public $timeFrom = '';
    public $timeTo = '';
    public $purpose = '';
    public $notes = '';
    public $guests = [];
    public $newGuest = '';

    public function mount()
    {
        $this->assetTypes = AssetType::where('is_active', true)->get();
        $this->scheduledDate = now()->format('Y-m-d');
        $this->timeFrom = '09:00';
        $this->timeTo = '10:00';
    }

    public function updatedSelectedAssetType()
    {
        if ($this->selectedAssetType) {
            $this->assetDetails = AssetDetail::where('asset_type_id', $this->selectedAssetType)
                ->where('is_active', true)
                ->get();
            $this->selectedAssetDetail = null;
        } else {
            $this->assetDetails = [];
            $this->selectedAssetDetail = null;
        }
    }

    public function addGuest()
    {
        if ($this->newGuest && trim($this->newGuest) !== '') {
            $this->guests[] = trim($this->newGuest);
            $this->newGuest = '';
        }
    }

    public function removeGuest($index)
    {
        unset($this->guests[$index]);
        $this->guests = array_values($this->guests);
    }

    public function save()
    {
        $this->validate([
            'selectedAssetType' => 'required|exists:asset_types,id',
            'selectedAssetDetail' => 'required|exists:asset_details,id',
            'scheduledDate' => 'required|date|after_or_equal:today',
            'timeFrom' => 'required',
            'timeTo' => 'required|after:timeFrom',
            'purpose' => 'required|min:5|max:500',
            'notes' => 'nullable|max:1000',
        ], [
            'selectedAssetType.required' => 'Please select an asset type.',
            'selectedAssetDetail.required' => 'Please select a specific asset.',
            'scheduledDate.required' => 'Please select a date for your booking.',
            'scheduledDate.after_or_equal' => 'Booking date cannot be in the past.',
            'timeFrom.required' => 'Please specify the start time.',
            'timeTo.required' => 'Please specify the end time.',
            'timeTo.after' => 'End time must be after start time.',
            'purpose.required' => 'Please provide a purpose for your booking.',
            'purpose.min' => 'Purpose must be at least 5 characters.',
        ]);

        try {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'asset_type_id' => $this->selectedAssetType,
                'asset_detail_id' => $this->selectedAssetDetail,
                'scheduled_date' => $this->scheduledDate,
                'time_from' => $this->timeFrom,
                'time_to' => $this->timeTo,
                'purpose' => $this->purpose,
                'notes' => $this->notes,
                'status' => 'pending',
                'guests_count' => count($this->guests),
                'guest_names' => !empty($this->guests) ? implode(', ', $this->guests) : null,
            ]);

            session()->flash('success', 'Booking created successfully! Your booking is now pending approval.');
            return redirect()->route('requester.bookings');

        } catch (\Exception $e) {
            session()->flash('error', 'There was an error creating your booking. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.requester.booking-create');
    }
}