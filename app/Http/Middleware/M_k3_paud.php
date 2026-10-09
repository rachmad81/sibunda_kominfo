<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class M_k3_paud
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
      if (in_array(Auth::User()->role, ['k3_paud', 'paud_sd', 'gugus', 'mitra'])) {
        return $next($request);
      } else {
        return redirect()->back()->with('alert-fail', 'Anda tidak berhak mengakses halaman ini!');
      }
    } else {
      return redirect(route('login'));
    }
  }
}
