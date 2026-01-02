<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    /**
     * Handle an incoming request.
     * Only allow users with role 'user' to access the route.
     * Admins will be redirected to admin dashboard.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', __('auth.loginRequired'));
        }

        // If user is admin, redirect to admin dashboard
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
