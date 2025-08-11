<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'assetType']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('asset_type_id')) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => "{$booking->purpose} ({$booking->assetType->name})",
                'start' => Carbon::parse($booking->scheduled_date)->format('Y-m-d') . 'T' . $booking->time_from,
                'end' => Carbon::parse($booking->scheduled_date)->format('Y-m-d') . 'T' . $booking->time_to,
                'color' => $this->getStatusColor($booking->status),
                'extendedProps' => [
                    'user' => $booking->user ? $booking->user->first_name . ' ' . $booking->user->last_name : 'Unknown',
                    'destination' => $booking->destination,
                    'status' => $booking->status,
                ],
            ];
        });

        return response()->json($events);
    }

    private function getStatusColor($status)
    {
        return match ($status) {
            'approved' => '#28a745',   // green
            'pending' => '#ffc107',    // yellow
            'rejected' => '#dc3545',   // red
            default => '#007bff',      // blue
        };
    }
}
