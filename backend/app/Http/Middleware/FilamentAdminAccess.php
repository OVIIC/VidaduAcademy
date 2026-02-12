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

        // DEBUG logging for every request
        \Illuminate\Support\Facades\Log::channel('stderr')->debug('DEBUG: Middleware Executing...');
        \Illuminate\Support\Facades\Log::channel('stderr')->debug('DEBUG: User ID: ' . auth()->id());
        \Illuminate\Support\Facades\Log::channel('stderr')->debug('DEBUG: Roles: ' . json_encode(auth()->user()->getRoleNames()));
        \Illuminate\Support\Facades\Log::channel('stderr')->debug('DEBUG: Has Admin Role: ' . (auth()->user()->hasRole('admin') ? 'YES' : 'NO'));

        // Check if user has admin panel access permission or admin role
        if (!auth()->user()->can('access admin panel') && !auth()->user()->hasRole('admin')) {
             \Illuminate\Support\Facades\Log::channel('stderr')->debug('DEBUG: BLOCKED by Middleware');
            abort(403, 'You do not have permission to access the admin panel.');
        }

        \Illuminate\Support\Facades\Log::channel('stderr')->debug('DEBUG: PASSED Middleware');

        return $next($request);
    }
}
