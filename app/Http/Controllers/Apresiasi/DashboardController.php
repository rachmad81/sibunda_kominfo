<?php

namespace App\Http\Controllers\Apresiasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, DB, Session;

class DashboardController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $data = [
          'main_menu' => 'dashboard',
        ];
        return view('apresiasi.dashboard.main', $data);
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
