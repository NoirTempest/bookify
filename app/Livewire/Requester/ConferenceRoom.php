<?php

namespace App\Livewire\Requester;

use Livewire\Component;
use App\Models\AssetDetail;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConferenceRoom extends Component
{
    public $conferenceRooms = [];
    public $showEnded = false; // Optional toggle to include ended bookings

    public function mount()
    {
        // Optionally update DB status for past bookings:
        // $this->markPastBookingsAsEnded();

        $this->loadApprovedBookings();
    }

    public function loadApprovedBookings()
    {
        $this->conferenceRooms = [];

        // Load all conference rooms (asset_type_id = 1)
        $rooms = AssetDetail::where('asset_type_id', 1)->get();

        foreach ($rooms as $room) {
            // Query approved bookings, optionally include ended if $showEnded = true
            $bookingsQuery = Booking::with('user')
                ->where('asset_name', $room->asset_name)
                ->where('asset_type_id', 1)
                ->where('status', 'approved')
                ->orderBy('scheduled_date')
                ->orderBy('time_from');

            if ($this->showEnded) {
                // Include ended bookings if toggled (optional)
                $bookingsQuery->orWhere('status', 'ended');
            }

            $bookings = $bookingsQuery->get()
                ->map(function ($booking) {
                    // Compose start and end DateTime with timezone Asia/Manila
                    $start = Carbon::parse($booking->scheduled_date)
                        ->setTimeFromTimeString($booking->time_from)
                        ->timezone('Asia/Manila');

                    $end = Carbon::parse($booking->scheduled_date)
                        ->setTimeFromTimeString($booking->time_to)
                        ->timezone('Asia/Manila');

                    // Fix end time if less than or equal to start
                    if ($end->lessThanOrEqualTo($start)) {
                        $end = $start->copy()->addMinutes(30);
                    }

                    $now = Carbon::now('Asia/Manila');

                    // Calculate timeline status independently of DB status
                    $timelineStatus = match (true) {
                        $now->between($start, $end) => 'Ongoing',
                        $now->lt($start) => 'Incoming',
                        default => 'Ended',
                    };

                    Log::info('Booking Debug:');
                    Log::info('Purpose: ' . ($booking->purpose ?? 'Untitled'));
                    Log::info('Start: ' . $start);
                    Log::info('End: ' . $end);
                    Log::info('Now: ' . $now);
                    Log::info('Timeline Status: ' . $timelineStatus);

                    return (object) [
                        'title' => $booking->purpose ?? 'Untitled',
                        'date' => $booking->scheduled_date->format('Y-m-d'),
                        'time_from' => $booking->time_from,
                        'time_to' => $booking->time_to,
                        'booked_by' => optional($booking->user)->first_name . ' ' . optional($booking->user)->last_name,
                        'timeline_status' => $timelineStatus,
                        'db_status' => $booking->status, // actual DB status ('approved', 'ended', etc)
                    ];
                });

            $this->conferenceRooms[] = (object) [
                'name' => $room->asset_name,
                'location' => $room->location,
                'bookings' => $bookings,
            ];
        }
    }

    /**
     * Optional: Update DB status to 'ended' for past bookings
     */
    public function markPastBookingsAsEnded()
    {
        $now = Carbon::now('Asia/Manila')->format('Y-m-d H:i');

        DB::update("
            UPDATE bookings 
            SET status = 'ended', updated_at = ?
            WHERE status = 'approved'
              AND STR_TO_DATE(CONCAT(scheduled_date, ' ', time_to), '%Y-%m-%d %H:%i') < ?
        ", [$now, $now]);
    }

    public function render()
    {
        return view('livewire.requester.conference-room');
    }
}
