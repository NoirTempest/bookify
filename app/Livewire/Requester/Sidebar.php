<?php

namespace App\Livewire\Requester;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Sidebar extends Component
{
    public $currentRoute;
    public $filter = 'all';

    public function mount()
    {
        $this->currentRoute = request()->route()->getName();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        $user = auth()->user();

        // Adjust this query to match your notifications table structure
        $query = $user->bookings(); // or $user->notifications() if using Laravel notifications

        switch ($this->filter) {
            case 'recent':
                $query->where('updated_at', '>=', now()->subDays(3));
                break;
            case 'approved':
                $query->where('status', 'approved');
                break;
            case 'rejected':
                $query->where('status', 'rejected');
                break;
        }

        $notifications = $query->latest()->paginate(10);

        return view('livewire.requester.sidebar', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count() ?? 0, // Adjust if not using notifications
            'filter' => $this->filter,
        ]);
    }
}
