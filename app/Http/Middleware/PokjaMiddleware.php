<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PokjaMiddleware
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
        if (Auth::check()) {
            if (Auth::User()->role == 'pokja') {
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
