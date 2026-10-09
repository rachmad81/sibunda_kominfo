<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BundaAdminMid
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
        // dd(Auth::check());
        if (Auth::check()) {
            if (Auth::User()->role == 'dinas' || Auth::User()->role == 'bunda' || Auth::User()->role == 'opd') {
                return $next($request);
            }
            else {
                return redirect()->back()->with('alert-fail','Anda tidak berhak mengakses halaman ini!');
            }
        }
        else{
            return redirect(route('login'));
        }
    }
}
