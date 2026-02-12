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
            error_log('DEBUG: User ID: ' . auth()->id());
            error_log('DEBUG: Roles: ' . auth()->user()->getRoleNames());
            error_log('DEBUG: Has Admin Role: ' . (auth()->user()->hasRole('admin') ? 'YES' : 'NO'));
            // Force re-reading from database to be sure
            error_log('DEBUG: Fresh User Roles: ' . auth()->user()->fresh()->getRoleNames());
            
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
