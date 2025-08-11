<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\AssetType;
use App\Models\AssetDetail;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CalendarModal extends Component
{
    public $selectedDate;
    public $timeFrom;
    public $timeTo;

    public $assetTypeId;
    public $assetDetailId;

    public $assetTypes = [];
    public $assetDetails = [];

    public $venue;
    public $purpose;
    public $destination;

    public $noOfSeats;
    public $notes;

    public function mount()
    {
        $this->assetTypes = AssetType::all();

        if ($this->assetTypeId) {
            $this->assetDetails = AssetDetail::where('asset_type_id', $this->assetTypeId)->get();
        }

        if ($this->assetDetailId) {
            $asset = AssetDetail::find($this->assetDetailId);
            if ($asset) {
                $this->venue = $asset->location;
            }
        }
    }

    public function updatedAssetTypeId($value)
    {
        $this->assetDetails = AssetDetail::where('asset_type_id', $value)->get();
        $this->assetDetailId = null;
        $this->venue = null;
    }

    public function updatedAssetDetailId($value)
    {
        $assetDetail = AssetDetail::find($value);
        if ($assetDetail) {
            $this->venue = $assetDetail->location;
        }
    }

    public function saveBooking()
    {
        Log::info('saveBooking method triggered');

        try {
            $this->validate([
                'selectedDate' => 'required|date',
                'timeFrom' => 'required',
                'timeTo' => 'required',
                'assetTypeId' => 'required|exists:asset_types,id',
                'assetDetailId' => 'required|exists:asset_details,id',
                'venue' => 'required|string',
                'purpose' => 'required|string',
                'destination' => 'required|string',
                'noOfSeats' => 'required|integer|min:1',
            ]);

            Log::info('Validation passed');

            $asset = AssetDetail::find($this->assetDetailId);

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'scheduled_date' => $this->selectedDate,
                'time_from' => $this->timeFrom,
                'time_to' => $this->timeTo,
                'asset_type_id' => $this->assetTypeId,
                'asset_detail_id' => $this->assetDetailId,
                'asset_name' => $asset->asset_name ?? 'N/A',
                'venue' => $asset->location ?? 'N/A',
                'purpose' => $this->purpose,
                'destination' => $this->destination,
                'no_of_seats' => $this->noOfSeats,
                'notes' => $this->notes,
                'status' => 'pending',
            ]);

            Log::info('Booking created successfully', ['booking_id' => $booking->id]);

            session()->flash('success', 'Booking created successfully!');
            $this->resetForm();
            $this->dispatch('bookingCreated');

        } catch (\Exception $e) {
            Log::error('Booking failed to save', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'input_data' => [
                    'selectedDate' => $this->selectedDate,
                    'timeFrom' => $this->timeFrom,
                    'timeTo' => $this->timeTo,
                    'assetTypeId' => $this->assetTypeId,
                    'assetDetailId' => $this->assetDetailId,
                    'venue' => $this->venue,
                    'purpose' => $this->purpose,
                    'destination' => $this->destination,
                    'noOfSeats' => $this->noOfSeats,
                    'notes' => $this->notes,
                ]
            ]);

            session()->flash('error', 'Failed to create booking. Please try again.');
        }
    }

    public function resetForm()
    {
        $this->selectedDate = null;
        $this->timeFrom = null;
        $this->timeTo = null;
        $this->assetTypeId = null;
        $this->assetDetailId = null;
        $this->assetDetails = [];
        $this->venue = null;
        $this->purpose = null;
        $this->destination = null;
        $this->noOfSeats = null;
        $this->notes = null;
    }

    public function render()
    {
        return view('livewire.requester.calendar-modal');
    }
}