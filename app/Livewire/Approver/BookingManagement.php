<?php

namespace App\Livewire\Approver;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprovedMail;
use Illuminate\Support\Facades\Log;


class BookingManagement extends Component
{
    use WithPagination;

    public $selectedBooking;
    public $disapproveReason = '';

    protected $listeners = ['viewBookingDetails'];

    public function viewBookingDetails($id)
    {
        $this->selectedBooking = Booking::with([
            'user.department',
            'user.branch',
            'assetType',
            'assetDetail',
            'bookedGuests'
        ])->findOrFail($id);

        $this->dispatch('open-details-modal');
    }

    public function approveBooking($bookingId)
    {
        $booking = Booking::with(['bookedGuests', 'user', 'assetDetail', 'assetType'])->findOrFail($bookingId);
        $user = Auth::user();
        $fullName = "{$user->first_name} {$user->last_name}";

        // Prevent duplicate approvals
        if ($booking->first_approver_name === $fullName || $booking->second_approver_name === $fullName) {
            $this->dispatch('approved-status', ['status' => 'already']);
            return;
        }

        // First approver logic
        if (!$booking->first_approver_name) {
            $booking->first_approver_name = $fullName;
            $booking->first_approved_at = now();
            $this->dispatch('approved-status', ['status' => 'first']);
        }
        // Second approver logic
        elseif (!$booking->second_approver_name) {
            $booking->second_approver_name = $fullName;
            $booking->second_approved_at = now();
            $booking->status = 'approved';
            $this->dispatch('approved-status', ['status' => 'second']);

            $booking->save();

            try {
                // Send to requester
                Log::info("Sending APPROVED email to requester: {$booking->user->email}");
                Mail::to($booking->user->email)
                    ->send(new BookingApprovedMail($booking, 'requester'));

                // Send to each guest (skip if same email as requester)
                foreach ($booking->bookedGuests as $guest) {
                    if ($guest->email !== $booking->user->email) {
                        Log::info("Sending GUEST email to: {$guest->email}");
                        Mail::to($guest->email)
                            ->send(new BookingApprovedMail($booking, 'guest', $guest->email));
                    } else {
                        Log::info("Skipped guest email because it's same as requester: {$guest->email}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Email sending failed: " . $e->getMessage());
            }
        } else {
            $this->dispatch('approved-status', ['status' => 'fully']);
            return;
        }

        $booking->save();
        $this->selectedBooking = $booking->fresh();
        $this->dispatch('close-details-modal');
    }



    public function openDisapproveModal($bookingId)
    {
        $this->selectedBooking = Booking::findOrFail($bookingId);
        $this->disapproveReason = '';
        $this->dispatch('open-disapprove-modal');
    }

    public function submitDisapproval()
    {
        $this->validate(['disapproveReason' => 'required|string|min:5']);

        if ($this->selectedBooking) {
            $this->selectedBooking->status = 'rejected';
            $this->selectedBooking->disapprove_reason = $this->disapproveReason;
            $this->selectedBooking->save();
            $this->dispatch('close-disapprove-modal');
            $this->dispatch('close-details-modal');
            $this->dispatch('disapproval-status', ['status' => 'done']);
            $this->disapproveReason = '';
        }
    }

    public function render()
    {
        return view('livewire.approver.booking-management', [
            'bookings' => Booking::with([
                'user.department',
                'user.branch',
                'assetType',
                'assetDetail',
                'bookedGuests'
            ])->latest()->paginate(10),
        ])->layout('layouts.approver');
    }
}
