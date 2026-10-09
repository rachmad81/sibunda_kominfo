<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
  /**
   * The path to the "home" route for your application.
   *
   * This is used by Laravel authentication to redirect users after login.
   *
   * @var string
   */
  public const HOME = '/landing/dashboard';

  /**
   * The controller namespace for the application.
   *
   * When present, controller route declarations will automatically be prefixed with this namespace.
   *
   * @var string|null
   */
  protected $namespace = 'App\\Http\\Controllers';

  /**
   * Define your route model bindings, pattern filters, etc.
   *
   * @return void
   */
  public function boot()
  {
    $this->configureRateLimiting();

    $this->routes(function () {
      Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/api.php'));

      Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/web.php'));

      Route::middleware(['web', 'dinasAuth'])
        ->namespace($this->namespace)
        ->group(base_path('routes/dinas.php'));

      Route::middleware(['web', 'bundaAuth'])
        ->namespace($this->namespace)
        ->group(base_path('routes/bunda.php'));

      Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/rekap.php'));

      Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/sekolah.php'));

      Route::middleware(['web', 'bundaAdmin'])
        ->namespace($this->namespace)
        ->group(base_path('routes/bundaadmin.php'));

      Route::middleware(['web', 'penilaiMid'])
        ->namespace($this->namespace)
        ->group(base_path('routes/penilai.php'));

      Route::middleware(['web', 'k3_paud_Auth'])
        ->namespace($this->namespace)
        ->group(base_path('routes/k3_paud.php'));

      Route::middleware(['web'])
        ->namespace($this->namespace)
        ->group(base_path('routes/monev.php'));

      Route::middleware(['web'])
        ->namespace($this->namespace)
        ->group(base_path('routes/apresiasi.php'));
    });
  }

  /**
   * Configure the rate limiters for the application.
   *
   * @return void
   */
  protected function configureRateLimiting()
  {
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
    });
  }
}
