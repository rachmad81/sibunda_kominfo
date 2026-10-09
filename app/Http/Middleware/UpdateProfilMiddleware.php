<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateProfilMiddleware
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
        if(Auth::check()){
            if(Auth::user()->role == 'dinas'){
                return redirect()->route('dinas-user-index');
            }
            elseif(Auth::User()->verified){
                return $next($request);
            }
            else{
                return redirect()->route('common-profil-index')->with('alert-warning','Anda harus mengisi data terlebih dahulu sebelum menggunakan Aplikasi!');
            }
        }
        else{
            return redirect()->route('login');
        }
    }
}
