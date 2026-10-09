<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IframeMiddleware
{
  public function handle($request, Closure $next)
  {
    $response = $next($request);

    $response->headers->remove('X-Frame-Options');

    $response->headers->set(
      'Content-Security-Policy',
      "frame-ancestors 'self' https://kantorku.surabaya.go.id https://wargaku.surabaya.go.id https://localhost/sibunda/public/"
    );

    return $response;
  }
}
