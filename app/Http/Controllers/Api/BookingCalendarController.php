<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class BookingCalendarController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $bookings = Booking::with(['user', 'assetType'])->get();

            $events = $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->assetType->name . ' - ' . $booking->user->first_name . ' ' . $booking->user->last_name,
                    'start' => $booking->scheduled_date . 'T' . $booking->time_from,
                    'end' => $booking->scheduled_date . 'T' . $booking->time_to,
                    'extendedProps' => [
                        'destination' => $booking->destination,
                        'status' => $booking->status,
                        'updated_at' => optional($booking->updated_at)->format('Y-m-d H:i:s'),
                    ],
                ];
            });

            return response()->json($events);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to load bookings.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

