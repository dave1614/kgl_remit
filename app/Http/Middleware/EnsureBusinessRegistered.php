<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->is_admin == 1) {
            return $next($request);
        }

        $registration = $user->businessRegistrations()->first();

        // If no registration exists, create empty registration (optional)
        if (!$registration) {
            $registration = $user->businessRegistrations()->create([]);
        }

        // Pending → redirect to limbo page
        if ($registration->status === 'pending') {
            // Prevent infinite loop
            if (!$request->routeIs('client.business.limbo')) {
                return redirect()->route('client.business.limbo', ['status' => 'pending']);
            }
        }

        // Rejected or new → allow access only to create/update page
        if ($registration->business_name === null || $registration->status === 'rejected') {
            if (!$request->routeIs('client.business.*')) {
                return redirect()->route('client.business.create')
                                 ->with('warning', 'Please complete your business registration first.');
            }
        }

        // Fully registered → redirect to dashboard if trying to access create page
        if ($registration->status === 'approved' && $user->business_registered) {
            if ($request->routeIs('client.business.create')) {
                return redirect()->route('dashboard')->with('success', 'Your business is fully registered.');
            }
        }

        return $next($request);
    }
}
