<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth, Redirect;

class Hakakses
{
  public function handle(Request $request, Closure $next, ...$role)
  {
    if (Auth::check()) {
      $peran = Auth::getUser()->role;
      if (count($role) != 0) {
        if (in_array($peran, $role)) {
          return $next($request);
        }
      }
    }

    return Redirect::to('/');
  }
}
