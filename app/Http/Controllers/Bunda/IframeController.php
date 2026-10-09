<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Auth;

class IframeController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;
    $data = [];
    $dt_token = DB::table('token_sayang_warga')->where('username', Auth::getUser()->username)->first();
    $token = $dt_token->token ?? 'token-314959afe03f5628d8ed4cb844ec620c';

    switch ($halaman) {
      case 'posyandu':
        $data = [
          'judul' => 'Halaman Posyandu',
          'sidebar' => 'dashboard_posyandu',
          'url' => 'https://sayangwarga.surabaya.go.id/ssobunda/' . $token,
        ];
        break;

      case 'pendampingan':
        $data = [
          'judul' => 'Halaman Pendampingan',
          'sidebar' => 'dashboard_pendampingan',
          'url' => 'https://sayangwarga.surabaya.go.id/monitoring-pendampingan',
        ];
        break;

      case 'akses_asw':
        $data = [
          'judul' => 'Akses ASW',
          'sidebar' => 'dashboard_akses',
          'url' => 'https://sayangwarga.surabaya.go.id/',
        ];
        break;

      case 'game_siappak':
        $data = [
          'judul' => 'Dashboard Game SIAP PPAK',
          'sidebar' => 'dashboard_game',
          'url' => 'https://ppa-dp3appkb.surabaya.go.id/embed/pemain-permainan?signature=9903285743da9e7c5691aaca4552e7de7ae5b22cca5f48916920f7fc98846e88',
        ];
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }

    return view('bunda.iframe.index', $data);
  }
}
