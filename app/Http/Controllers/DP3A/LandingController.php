<?php

namespace App\Http\Controllers\DP3A;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class LandingController extends Controller
{
  public function iframe(Request $request)
  {
    $aksi = $request->aksi;
    $data = [
      'menu_active' => 'dp3a',
    ];
    switch ($aksi) {
      case 'permainan':
        $url = 'https://ppa-dp3appkb.surabaya.go.id/game/';
        return Redirect::to($url);
        break;

      case 'telekonsultasi':
        $url = 'https://ppa-dp3appkb.surabaya.go.id/konsultasi/';
        return Redirect::to($url);
        break;

      default:
        # code...
        break;
    }
  }
}
