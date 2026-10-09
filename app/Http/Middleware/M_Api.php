<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class M_Api
{
  public function handle(Request $request, Closure $next)
  {
    $header = $request->header();
    $key = $header['key'][0];
    if ($key == '5|JZF8uZ^A#+L@r*o($]^I><g6(h+?&4') {
      return $next($request);
    } else {
      return ['code' => '403', 'message' => '403 - Forbidden'];
    }
  }
}
