<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $recipientType;
    public $guestEmail;

    public function __construct(Booking $booking, string $recipientType = 'requester', string $guestEmail = null)
    {
        // Load only necessary relationships (vehicleAssignment is empty at this point)
        $this->booking = $booking->load([
            'user',
            'assetType',
            'assetDetail',
        ]);

        $this->recipientType = $recipientType;
        $this->guestEmail = $guestEmail;
    }

    public function build()
    {
        Log::info("📧 BookingApprovedMail triggered", [
            'to' => $this->recipientType === 'guest' ? $this->guestEmail : $this->booking->user->email,
            'recipientType' => $this->recipientType,
            'asset_type' => $this->booking->assetType->name ?? 'unknown',
        ]);

        $subject = $this->recipientType === 'guest'
            ? 'You’re a guest in an approved booking'
            : 'Your booking has been approved';

        return $this->subject($subject)
            ->view('emails.booking-approved')
            ->with([
                'booking' => $this->booking,
                'recipientType' => $this->recipientType,
                'guestEmail' => $this->guestEmail,
            ]);
    }
}
