<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;


#[Layout('layouts.guest')]
class Login extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        // Get user info for debugging
        $user = auth()->user();
        $role = $user->role;

        // Redirect based on user role
        $redirectUrl = \App\Services\RoleRedirectService::getRedirectUrl();

        // Enhanced logging for debugging
        Log::info('Livewire login successful', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->full_name ?? 'N/A',
            'role_id' => $role?->id,
            'role_name' => $role?->name,
            'redirect_url' => $redirectUrl,
            'is_admin' => $role?->name === 'Admin',
            'session_id' => session()->getId()
        ]);

        // Log before redirect
        Log::info('About to redirect', [
            'redirect_url' => $redirectUrl,
            'user_role' => $role?->name
        ]);

        // Use direct redirect for role-based routing
        // This ensures proper navigation to admin/approver/requester dashboards
        $this->redirect($redirectUrl, navigate: false);
    }

    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
