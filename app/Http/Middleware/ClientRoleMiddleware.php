<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->hasRole('Client') ||Auth::user()->hasRole('admin') || Auth::user()->hasRole('dispatcher') )) {
            return $next($request);
        }

        // dd(99);
        return redirect('/')->with('error', 'Access denied.');
    }
}
