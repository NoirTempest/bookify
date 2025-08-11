<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\AssetType;
use App\Models\AssetDetail;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ConferenceRoomBooking extends Component
{
    public $scheduled_date;
    public $asset_type_id;
    public $asset_detail_id;
    public $time_from;
    public $time_to;
    public $purpose;
    public $destination;
    public $notes;
    public $no_of_seats;

    public $assetTypes = [];
    public $assetDetails = [];

    protected $listeners = ['calendarDateClicked' => 'openBookingModal'];

    public function mount()
    {
        $this->assetTypes = AssetType::all();
    }

    public function updatedAssetTypeId()
    {
        $this->assetDetails = AssetDetail::where('asset_type_id', $this->asset_type_id)->get();
    }

    public function openBookingModal($payload)
    {
        // Extract date & time from JS payload
        $this->scheduled_date = substr($payload['start'], 0, 10);
        $this->time_from = substr($payload['start'], 11, 5);
        $this->time_to = substr($payload['end'], 11, 5);

        // Reset other fields
        $this->asset_type_id = null;
        $this->asset_detail_id = null;
        $this->purpose = '';
        $this->destination = '';
        $this->notes = '';
        $this->no_of_seats = null;

        $this->dispatch('show-booking-modal');
    }

    public function submit()
    {
        $this->validate([
            'scheduled_date' => 'required|date',
            'asset_type_id' => 'required|exists:asset_types,id',
            'asset_detail_id' => 'required|exists:asset_details,id',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i|after:time_from',
            'purpose' => 'required|string',
            'destination' => 'required|string',
            'no_of_seats' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        Booking::create([
            'user_id' => Auth::id(),
            'asset_type_id' => $this->asset_type_id,
            'asset_detail_id' => $this->asset_detail_id,
            'scheduled_date' => $this->scheduled_date,
            'time_from' => $this->time_from,
            'time_to' => $this->time_to,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'notes' => $this->notes,
            'status' => 'pending',
            'no_of_seats' => $this->no_of_seats,
            'asset_name' => AssetDetail::find($this->asset_detail_id)->asset_name ?? null,
        ]);

        $this->dispatch('close-booking-modal');
        $this->dispatch('booking-saved');
    }

    public function render()
    {
        return view('livewire.requester.conference-room-booking');
    }
}
