<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\Booking;
use App\Models\AssetDetail;
use App\Models\AssetType;
use Carbon\Carbon;

class Calendar extends Component
{
    public $events = [];
    public $assetOptions = [];

    public function mount()
    {
        $this->loadEvents();
        $this->assetOptions = AssetDetail::select('id', 'asset_name', 'plate_number')->get();
    }

    public function loadEvents()
    {
        $bookings = Booking::with([
            'user',
            'assetType',
            'vehicleDriverAssignments.driver'
        ])
            ->where('status', 'approved')
            ->orderBy('scheduled_date')
            ->get();

        $this->events = $bookings->filter(
            fn($booking) =>
            !empty ($booking->scheduled_date) &&
            !empty ($booking->time_from) &&
            !empty ($booking->time_to)
        )->map(function ($booking) {
            $start = Carbon::parse($booking->scheduled_date)->setTimeFrom($booking->time_from);
            $end = Carbon::parse($booking->scheduled_date)->setTimeFrom($booking->time_to);
            $now = Carbon::now('Asia/Manila');

            $timelineStatus = $now->between($start, $end)
                ? 'Ongoing'
                : ($now->gt($end) ? 'Ended' : 'Incoming');

            $timeRange = $start->format('g:i A') . ' > ' . $end->format('g:i A');
            $user = optional($booking->user);
            $requestedBy = trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''));

            $assetTypeName = optional($booking->assetType)->name;
            $isVehicle = strtolower($assetTypeName) === 'vehicle';

            $driverName = $isVehicle
                ? optional($booking->vehicleDriverAssignments->first()?->driver)->name
                : null;

            return [
                'title' => $isVehicle
                    ? ($driverName ? $driverName : 'Unassigned Driver')  // 👈 driver name as title
                    : ($booking->asset_name ?? 'No Asset Name'),

                'start' => $start->format('Y-m-d\TH:i:s'),
                'end' => $end->format('Y-m-d\TH:i:s'),
                'allDay' => false,
                'asset_detail_id' => $booking->asset_detail_id,
                'asset_name' => $booking->asset_name,
                'asset_type' => $assetTypeName,
                'is_vehicle' => $isVehicle,
                'driver_name' => $driverName,
                'time_range' => $timeRange,
                'requested_by' => $requestedBy ?: 'Unknown User',
                'purpose' => $booking->purpose ?? '',
                'venue' => $booking->destination ?? 'N/A',
                'status' => $booking->status,
                'timeline_status' => $timelineStatus,
            ];

        })->values()->toArray();
    }

    public function render()
    {
        return view('livewire.requester.calendar', [
            'events' => $this->events,
            'assetOptions' => $this->assetOptions,
            'assetTypes' => AssetType::all(), // ✅ Add this line
            'assetDetails' => AssetDetail::all(), // ✅ Add this line

        ]);
    }
}

