<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\AssetType;
use App\Models\AssetDetail;
use App\Models\Booking;
use App\Models\BookedGuest;
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

    public $vehicle_type_id = null;
    public $conference_type_id = null;

    public $guests = [''];

    protected $listeners = ['calendarDateClicked' => 'openBookingModal'];

    public function mount()
    {
        $this->assetTypes = AssetType::all();

        // Determine well-known type IDs
        $this->vehicle_type_id = AssetType::where('name', 'Vehicle')->value('id');
        $this->conference_type_id = AssetType::where('name', 'Conference Room')->value('id');

        // Default to Conference Room if available
        if (!$this->asset_type_id) {
            $this->asset_type_id = $this->conference_type_id ?? ($this->assetTypes->first()->id ?? null);
        }

        // Preload asset details for the selected type
        if ($this->asset_type_id) {
            $this->assetDetails = AssetDetail::where('asset_type_id', $this->asset_type_id)->get();
        } else {
            $this->assetDetails = collect();
        }
    }

    public function updatedAssetTypeId()
    {
        $this->assetDetails = AssetDetail::where('asset_type_id', $this->asset_type_id)->get();
    }

    public function updated($property, $value)
    {
        if ($property === 'asset_type_id') {
            $this->assetDetails = AssetDetail::where('asset_type_id', $this->asset_type_id)->get();
            $this->asset_detail_id = null;
            $this->destination = null;
        }
    }

    public function updatedAssetDetailId($value)
    {
        if ($value) {
            $detail = AssetDetail::find($value);
            if ($detail) {
                $this->destination = $detail->location;
            }
        }
    }

    public function addGuest()
    {
        $this->guests[] = '';
    }

    public function removeGuest($index)
    {
        if (isset($this->guests[$index])) {
            unset($this->guests[$index]);
            $this->guests = array_values($this->guests);
        }
    }

    public function openBookingModal($payload)
    {
        // Extract date & time from JS payload
        $this->scheduled_date = substr($payload['start'], 0, 10);
        $this->time_from = substr($payload['start'], 11, 5);
        $this->time_to = substr($payload['end'], 11, 5);

        // Reset selection state but keep default or current asset type
        $this->asset_detail_id = null;
        $this->purpose = '';
        $this->destination = '';
        $this->notes = '';
        $this->no_of_seats = null;
        $this->guests = [''];

        if ($this->asset_type_id) {
            $this->assetDetails = AssetDetail::where('asset_type_id', $this->asset_type_id)->get();
        }

        $this->dispatch('show-booking-modal');
    }

    public function submit()
    {
        $rules = [
            'scheduled_date' => 'required|date',
            'asset_type_id' => 'required|exists:asset_types,id',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i|after:time_from',
            'purpose' => 'nullable|string',
            'destination' => 'required|string',
            'no_of_seats' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'guests' => 'array',
            'guests.*' => 'nullable|email',
        ];

        // Require asset_detail_id when booking Conference Room
        if ((int) $this->asset_type_id === (int) ($this->conference_type_id ?? -1)) {
            $rules['asset_detail_id'] = 'required|exists:asset_details,id';
        } else {
            $rules['asset_detail_id'] = 'nullable|exists:asset_details,id';
        }

        $this->validate($rules);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'asset_type_id' => $this->asset_type_id,
            'asset_detail_id' => $this->asset_detail_id,
            'scheduled_date' => $this->scheduled_date,
            'time_from' => $this->time_from,
            'time_to' => $this->time_to,
            'purpose' => $this->purpose ?? '',
            'destination' => $this->destination,
            'notes' => $this->notes,
            'status' => 'pending',
            'no_of_seats' => $this->no_of_seats,
            'asset_name' => $this->asset_detail_id ? (AssetDetail::find($this->asset_detail_id)->asset_name ?? null) : null,
        ]);

        // Save guests
        foreach ($this->guests as $email) {
            $email = trim((string) $email);
            if ($email !== '') {
                BookedGuest::create([
                    'booking_id' => $booking->id,
                    'email' => $email,
                ]);
            }
        }

        $this->dispatch('close-booking-modal');
        $this->dispatch('booking-saved');
    }

    public function render()
    {
        return view('livewire.requester.conference-room-booking');
    }
}
