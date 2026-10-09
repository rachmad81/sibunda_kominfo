<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use DB, DataTables, Hash, Auth;

class SewaController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;
    switch ($halaman) {
      case 'index':
        $tanggal = date('Y-m-d');
        $awal = date('Y-m-01', strtotime($tanggal));
        $akhir = date('Y-m-t', strtotime($tanggal));
        $judul = 'Data sewa gedung Graha PAUD tanggal ' . $awal . ' s.d ' . $akhir;
        $data = [
          'tanggal' => $tanggal,
          'judul' => $judul,
        ];
        return view('dinas.sewa.index', $data);
        break;
      case 'get_data':
        $status = $request->status; //null
        $tanggal = $request->tanggal; //"2026-02-20"
        $awal = date('Y-m-01', strtotime($tanggal));
        $akhir = date('Y-m-t', strtotime($tanggal));

        $sewa = DB::table('public.sewa_graha')
          ->whereRaw("tanggal BETWEEN '$awal' AND '$akhir'")
          ->when($status != '', function ($w) use ($status) {
            return $w->where('status', $status);
          })
          ->orderByRaw("tanggal,jam,CASE WHEN status='Pengajuan' THEN 0 ELSE 1 END");

        return DataTables::of($sewa)
          ->addIndexColumn()
          ->addColumn('status_baru', function ($item) {
            switch ($item->status) {
              case 'Pengajuan':
                $badge = 'badge-warning';
                break;
              case 'Setujui':
                $badge = 'badge-success';
                break;

              default:
                $badge = 'badge-danger';
                break;
            }
            $html = '<a href="javascript:void(0)" class="badge ' . $badge . '">' . $item->status . '</a>';
            return $html;
          })->addColumn('aksi', function ($item) {
            $html = '';
            switch ($item->status) {
              case 'Pengajuan':
                $html .= '<a href="javascript:void(0)" onclick="ubah_status(\'' . $item->id . '\',\'Setujui\')" class="btn btn-sm btn-success">Setujui</a>';
                $html .= '<a href="javascript:void(0)" onclick="ubah_status(\'' . $item->id . '\',\'Tolak\')" class="btn btn-sm btn-danger">Tolak</a>';
                break;
              case 'Setujui':
                $html .= '<a href="javascript:void(0)" onclick="ubah_status(\'' . $item->id . '\',\'Tolak\')" class="btn btn-sm btn-danger">Tolak</a>';
                break;
              case 'Tolak':
                $html .= '<a href="javascript:void(0)" onclick="ubah_status(\'' . $item->id . '\',\'Setujui\')" class="btn btn-sm btn-success">Setujui</a>';
                break;

              default:
                # code...
                break;
            }
            return $html;
          })
          ->rawColumns(['status_baru', 'aksi'])
          ->make(true);
        break;

      case 'simpan':
        $id = $request->id; //"6"
        $status = $request->status; //"Tolak"

        $simpan = DB::table('public.sewa_graha')->where('id', $id)->update(['status' => $status]);
        if ($simpan) {
          return ['code' => 200, 'pesan' => 'Berhasil diupdate'];
        } else {
          return ['code' => 250, 'pesan' => 'Gagal diupdate'];
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
