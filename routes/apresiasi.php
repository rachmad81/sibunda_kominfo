<?php

use Illuminate\Support\Facades\Route;

Route::name('apresiasi-')->prefix('apresiasi')->group(function () {
  // DASHBOARD
  Route::match(['post', 'get'], 'dashboard/{halaman}', 'Apresiasi\DashboardController@halaman')->name('dashboard');
});
