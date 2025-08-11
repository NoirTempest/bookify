<?php

namespace App\Livewire\Approver;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingApprovedMail;

class VehicleBookingManagement extends Component
{
    use WithPagination;

    public $selectedBooking;
    public $disapproveReason = '';

    protected $listeners = ['viewBookingDetails', 'resetSelectedBooking'];

    public function viewBookingDetails($id)
    {
        $this->selectedBooking = Booking::with([
            'user.department',
            'user.branch',
            'assetType',
            'assetDetail',
            'bookedGuests',
            'vehicleAssignment.assetDetail',
            'vehicleAssignment.driver'
        ])->findOrFail($id);

        $this->dispatch('open-details-modal');
    }

    public function approveBooking($bookingId)
    {
        logger('🟡 approveBooking called (vehicle)', ['bookingId' => $bookingId]);

        $booking = Booking::with(['bookedGuests', 'user', 'assetType', 'assetDetail'])->findOrFail($bookingId);
        $user = Auth::user();
        $fullName = $user->first_name . ' ' . $user->last_name;

        logger('👤 Current approver', ['user' => $fullName]);

        if (
            $booking->first_approver_name === $fullName ||
            $booking->second_approver_name === $fullName
        ) {
            logger('⚠️ Already approved by this user');
            $this->dispatch('approved-status', ['status' => 'already']);
            return;
        }

        if (!$booking->first_approver_name) {
            logger('✅ First approval assigned');
            $booking->first_approver_name = $fullName;
            $booking->first_approved_at = now();

            $this->dispatch('approved-status', ['status' => 'first']);
        } elseif (!$booking->second_approver_name) {
            logger('✅ Second approval assigned');
            $booking->second_approver_name = $fullName;
            $booking->second_approved_at = now();
            $booking->status = 'approved';

            $this->dispatch('approved-status', ['status' => 'second']);

            // ✅ Send emails after full approval
            try {
                Log::info("📧 Sending email to requester: {$booking->user->email}");
                Mail::to($booking->user->email)->send(new BookingApprovedMail($booking, 'requester'));

                foreach ($booking->bookedGuests as $guest) {
                    if ($guest->email !== $booking->user->email) {
                        Log::info("📧 Sending email to guest: {$guest->email}");
                        Mail::to($guest->email)->send(new BookingApprovedMail($booking, 'guest', $guest->email));
                    } else {
                        Log::info("⏭ Skipped guest email (same as requester): {$guest->email}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("❌ Failed to send booking emails: " . $e->getMessage());
            }
        } else {
            logger('ℹ️ Booking already fully approved');
            $this->dispatch('approved-status', ['status' => 'fully']);
            return;
        }

        $booking->save();

        $this->selectedBooking = $booking->fresh([
            'user.department',
            'user.branch',
            'assetType',
            'assetDetail',
            'bookedGuests',
            'vehicleAssignment.assetDetail',
            'vehicleAssignment.driver'
        ]);

        logger('💾 Vehicle booking saved and refreshed', [
            'first_approver' => $booking->first_approver_name,
            'second_approver' => $booking->second_approver_name,
            'status' => $booking->status,
        ]);

        $this->dispatch('close-details-modal');
        $this->dispatch('bookingUpdated');
    }

    public function openDisapproveModal($bookingId)
    {
        $this->selectedBooking = Booking::findOrFail($bookingId);
        $this->disapproveReason = '';

        $this->dispatch('open-disapprove-modal');
    }

    public function submitDisapproval()
    {
        $this->validate([
            'disapproveReason' => 'required|string|min:5',
        ]);

        if ($this->selectedBooking) {
            $this->selectedBooking->update([
                'status' => 'rejected',
                'disapprove_reason' => $this->disapproveReason,
            ]);

            $this->dispatch('close-disapprove-modal');
            $this->dispatch('close-details-modal');
            $this->dispatch('disapproval-status', ['status' => 'done']);

            $this->disapproveReason = '';
            $this->dispatch('bookingUpdated');
        }
    }

    public function resetSelectedBooking()
    {
        $this->selectedBooking = null;
        $this->disapproveReason = '';
    }

    public function render()
    {
        return view('livewire.approver.vehicle-booking-management', [
            'bookings' => Booking::with([
                'user.department',
                'user.branch',
                'assetType',
                'assetDetail',
                'bookedGuests'
            ])
                ->where('asset_type_id', 2) // Only vehicle bookings
                ->latest()
                ->paginate(10),
        ])->layout('layouts.approver');
    }
}
