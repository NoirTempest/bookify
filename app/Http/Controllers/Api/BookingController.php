<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('user', 'assetType');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->asset_type_id) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $events = $query->get()->map(function ($booking) {
            return [
                'title' => $booking->title ?? 'Booking',
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'color' => $booking->status === 'approved' ? 'green' :
                           ($booking->status === 'pending' ? 'orange' : 'red')
            ];
        });

        return response()->json($events);
    }
}
