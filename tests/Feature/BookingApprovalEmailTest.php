<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprovedMail;
use App\Models\User;
use App\Models\Booking;
use App\Models\BookedGuest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingApprovalEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_email_to_requester_and_guest_when_fully_approved()
    {
        Mail::fake();

        // Create user/requester & approver
        $requester = User::factory()->create(['email' => 'christian.delapos6@gmail.com']);
        $approver1 = User::factory()->create();
        $approver2 = User::factory()->create();

        // Create bookings and guests
        $booking = Booking::factory()->create(['user_id' => $requester->id]);
        $guest = BookedGuest::factory()->create([
            'booking_id' => $booking->id,
            'email' => 'sawaljiahmae24@gmail.com'
        ]);

        // Act: simulate two approvals
        $this->actingAs($approver1)->post("/approver/booking/{$booking->id}/approve");
        $this->actingAs($approver2)->post("/approver/booking/{$booking->id}/approve");

        // Assert emails were queued to both
        Mail::assertQueued(BookingApprovedMail::class, function ($mail) {
            return $mail->recipientType === 'requester';
        });
        Mail::assertQueued(BookingApprovedMail::class, function ($mail) {
            return $mail->recipientType === 'guest';
        });
    }
}
