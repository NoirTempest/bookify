<?php

namespace App\Http\Controllers;

use App\Models\BookedGuest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BookedGuestController extends Controller
{
    public function index(): JsonResponse
    {
        $bookedGuests = BookedGuest::with('booking.user')->get();

        return response()->json([
            'success' => true,
            'data' => $bookedGuests,
        ]);
    }

    public function create(): JsonResponse
    {
        $bookings = Booking::with(['user', 'assetDetail'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'email' => 'required|email|max:255',
            ]);

            // Check if booking exists and is not cancelled
            $booking = Booking::find($validated['booking_id']);
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add guests to cancelled booking',
                ], 422);
            }

            // Check if guest email already exists for this booking
            $existingGuest = BookedGuest::where('booking_id', $validated['booking_id'])
                ->where('email', $validated['email'])
                ->first();

            if ($existingGuest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guest email already exists for this booking',
                ], 422);
            }

            $bookedGuest = BookedGuest::create($validated);
            $bookedGuest->load('booking.user');

            return response()->json([
                'success' => true,
                'message' => 'Booked guest created successfully',
                'data' => $bookedGuest,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(BookedGuest $bookedGuest): JsonResponse
    {
        $bookedGuest->load('booking.user');

        return response()->json([
            'success' => true,
            'data' => $bookedGuest,
        ]);
    }

    public function edit(BookedGuest $bookedGuest): JsonResponse
    {
        $bookings = Booking::with(['user', 'assetDetail'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'booked_guest' => $bookedGuest->load('booking'),
                'bookings' => $bookings,
            ],
        ]);
    }

    public function update(Request $request, BookedGuest $bookedGuest): JsonResponse
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'email' => 'required|email|max:255',
            ]);

            // Check if booking exists and is not cancelled
            $booking = Booking::find($validated['booking_id']);
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update guests for cancelled booking',
                ], 422);
            }

            // Check if guest email already exists for this booking (excluding current record)
            $existingGuest = BookedGuest::where('booking_id', $validated['booking_id'])
                ->where('email', $validated['email'])
                ->where('id', '!=', $bookedGuest->id)
                ->first();

            if ($existingGuest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guest email already exists for this booking',
                ], 422);
            }

            $bookedGuest->update($validated);
            $bookedGuest->load('booking.user');

            return response()->json([
                'success' => true,
                'message' => 'Booked guest updated successfully',
                'data' => $bookedGuest,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(BookedGuest $bookedGuest): JsonResponse
    {
        // Check if booking is not cancelled
        if ($bookedGuest->booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove guests from cancelled booking',
            ], 422);
        }

        $bookedGuest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booked guest deleted successfully',
        ]);
    }

    /**
     * Get guests for a specific booking
     */
    public function getGuestsByBooking(Booking $booking): JsonResponse
    {
        $guests = $booking->bookedGuests;

        return response()->json([
            'success' => true,
            'data' => $guests,
        ]);
    }

    /**
     * Add multiple guests to a booking
     */
    public function addMultipleGuests(Request $request, Booking $booking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'emails' => 'required|array|min:1',
                'emails.*' => 'required|email|max:255',
            ]);

            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add guests to cancelled booking',
                ], 422);
            }

            $addedGuests = [];
            $duplicateEmails = [];

            foreach ($validated['emails'] as $email) {
                // Check if guest already exists
                $existingGuest = BookedGuest::where('booking_id', $booking->id)
                    ->where('email', $email)
                    ->first();

                if ($existingGuest) {
                    $duplicateEmails[] = $email;
                } else {
                    $guest = BookedGuest::create([
                        'booking_id' => $booking->id,
                        'email' => $email,
                    ]);
                    $addedGuests[] = $guest;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Guests processed successfully',
                'data' => [
                    'added_guests' => $addedGuests,
                    'duplicate_emails' => $duplicateEmails,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
