<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$pages): Response
    {
        // Must be logged in
        if (!Auth::check()) {
            return redirect(route('login'));
        }

        $user = Auth::user();

        if ($user->is_active === false) {
            return redirect(route('home'));
        }

        // Admin has superuser access to everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check matching role permission for any of the page keys
        $isAllowed = RolePermission::where('role', $user->role)
            ->whereIn('page', $pages)
            ->where('is_allowed', true)
            ->exists();

        if (!$isAllowed) {
            abort(403, 'Access Denied: You do not have permission to view this page.');
        }

        return $next($request);
    }
}
