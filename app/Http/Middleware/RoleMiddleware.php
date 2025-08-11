<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has an assigned role
        if (!$user->role) {
            abort(403, 'Access denied. No role assigned.');
        }

        $userRole = $user->role->name;

        // Check if user's role is in the allowed roles
        if (!in_array($userRole, $roles)) {
            abort(403, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}