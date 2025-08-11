<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Use role-based redirection
            $redirectUrl = \App\Services\RoleRedirectService::getRedirectUrl();
            return redirect()->intended($redirectUrl . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Use role-based redirection
        $redirectUrl = \App\Services\RoleRedirectService::getRedirectUrl();
        return redirect()->intended($redirectUrl . '?verified=1');
    }
}
