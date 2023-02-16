<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class otpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            if ($user->role != 'AGENCY') {
                if ($user->role == 'ADMIN') {
                    return redirect('/dashboard');
                } else {
                    return abort(401);
                }
            }
            return redirect('/agencyDashboard');
        }
        return $next($request);
    }
}