<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Check if user has admin panel access permission or admin role
        if (!auth()->user()->can('access admin panel') && !auth()->user()->hasRole('admin')) {
             throw new \Exception('DEBUGGING_MIDDLEWARE_REACHED: UserID: ' . auth()->id() . ' | Roles: ' . json_encode(auth()->user()->getRoleNames()));
            
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
