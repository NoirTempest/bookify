<?php

namespace App\Livewire\Admin\Layout;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class Navigation extends Component
{
    public function mount()
    {
        // Ensure only admin users can access this navigation
        if (!auth()->check() || !auth()->user()->role || auth()->user()->role->name !== 'Admin') {
            abort(403, 'Access denied. Admin access required.');
        }
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        // Redirect to login page after logout
        $this->redirect('/login', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.layout.navigation');
    }
}