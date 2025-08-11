<?php

namespace App\Livewire\Requester;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public $pageTitle = 'Dashboard';

    public function mount()
    {
        $this->setPageTitle();
    }

    public function setPageTitle()
    {
        $route = request()->route()->getName();

        switch ($route) {
            case 'requester.dashboard':
                $this->pageTitle = 'Dashboard';
                break;
            case 'requester.bookings':
                $this->pageTitle = 'My Bookings';
                break;
            case 'requester.bookings.create':
                $this->pageTitle = 'New Booking';
                break;
            case 'requester.calendar':
                $this->pageTitle = 'Calendar';
                break;
            case 'profile.edit':
                $this->pageTitle = 'Profile Settings';
                break;
            case 'requester.notifications':
                $this->pageTitle = 'Notifications';
                break;
            default:
                $this->pageTitle = 'Dashboard';
        }
    }

    public function render()
    {
        return view('livewire.requester.header');
    }
}