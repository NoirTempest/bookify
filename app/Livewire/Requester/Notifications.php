<?php

namespace App\Livewire\Requester;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.requester')]
class Notifications extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, recent, approved, rejected

    public function markAsRead($bookingId)
    {
        // For bookings-based notifications, we could add a 'notification_read_at' column
        // For now, this is just a placeholder
        session()->flash('success', 'Notification acknowledged.');
    }

    public function markAllAsRead()
    {
        // Mark all recent notifications as acknowledged
        session()->flash('success', 'All notifications acknowledged.');
    }

    public function viewBooking($bookingId)
    {
        // Redirect to booking details or show modal
        return redirect()->route('requester.bookings.show', $bookingId);
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();

        // Build notification-style data from bookings
        $query = Booking::with(['assetType', 'assetDetail'])
            ->where('user_id', $userId);

        switch ($this->filter) {
            case 'recent':
                // Recent activity (last 7 days)
                $query = $query->where('updated_at', '>=', now()->subDays(7));
                break;
            case 'approved':
                // Recently approved bookings
                $query = $query->where('status', 'approved')
                    ->where('updated_at', '>=', now()->subDays(14));
                break;
            case 'rejected':
                // Recently rejected bookings
                $query = $query->where('status', 'rejected')
                    ->where('updated_at', '>=', now()->subDays(14));
                break;
            default:
                // All notifications (upcoming approved + recent status changes)
                $query = $query->where(function ($q) {
                    $q->where('status', 'approved')
                        ->where('scheduled_date', '>=', now()->toDateString())
                        ->where('scheduled_date', '<=', now()->addDays(7)->toDateString());
                })->orWhere(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->whereIn('status', ['rejected', 'approved'])
                        ->where('updated_at', '>=', now()->subDays(7));
                });
                break;
        }

        $notifications = $query->orderBy('updated_at', 'desc')->paginate(10);

        // Count of important notifications
        $unreadCount = Booking::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->where('scheduled_date', '>=', now()->toDateString())
                    ->where('scheduled_date', '<=', now()->addDays(7)->toDateString());
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'rejected')
                    ->where('updated_at', '>=', now()->subDays(3));
            })
            ->count();

        return view('livewire.requester.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }
}