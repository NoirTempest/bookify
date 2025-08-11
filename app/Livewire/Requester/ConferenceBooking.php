<?php 

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\AssetType;
use App\Models\AssetDetail;

class ConferenceBooking extends Component
{
    public $asset_type_id;
    public $asset_detail_id;
    public $assetTypes = [];
    public $assetDetails = [];
    public $venue;

    public $purpose;
    public $no_of_seats;
    public $scheduled_date;
    public $time_from;
    public $time_to;

    public function mount()
    {
        $this->assetTypes = AssetType::all();
    }

    public function updatedAssetTypeId()
    {
        // Filter asset details based on the selected type
        $this->assetDetails = AssetDetail::where('asset_type_id', $this->asset_type_id)->get();
        $this->asset_detail_id = null;
        $this->venue = null;
    }

    public function updatedAssetDetailId()
    {
        // Automatically set venue (location) when room is selected
        $detail = AssetDetail::find($this->asset_detail_id);
        $this->venue = $detail?->location;
    }

    public function submitBooking()
    {
        // Your validation and booking creation logic here
    }

    public function render()
    {
        return view('livewire.requester.conference-booking');
    }
}
