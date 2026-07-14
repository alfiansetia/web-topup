<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordSet
{
    /**
     * Handle an incoming request.
     *
     * Force users who logged in via Google (no password) to set one.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && empty($user->getOriginal('password'))) {
            if (!$request->routeIs('password.set') && !$request->routeIs('password.set.store')) {
                return redirect()->route('password.set');
            }
        }

        return $next($request);
    }
}
