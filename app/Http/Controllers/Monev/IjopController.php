<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tools\Bantuan;
use Auth, Redirect, Session, DB, DataTables, Storage;

class IjopController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::table('cutoff_rekap.monitoring_ijop')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.ijop.main', $data);
        break;

      case 'get_data':
        $tahun_monev = $request->tahun_monev;
        $status_monev = $request->status_monev;

        $ijop = DB::table('public.ijops')
          ->selectRaw("npsn,selesai_berlaku,no_izin_operasional")
          ->get();

        $ijop = $ijop->keyBy('npsn');

        $monev = DB::table('cutoff_rekap.monitoring_ijop as jp')
          ->where('tahun', $tahun_monev)
          ->get();

        $npsn = collect($monev)->pluck('npsn')->toArray();

        $sekolah = MasterController::data_sekolah($request);
        $sekolah = $sekolah->when($status_monev != 'all', function ($w) use ($status_monev, $npsn) {
          if ($status_monev == 'Belum') {
            return $w->whereNotIn('s.npsn', $npsn);
          } else {
            return $w->whereIn('s.npsn', $npsn);
          }
        });

        return DataTables::queryBuilder($sekolah)
          ->addIndexColumn()
          ->addColumn('aksi', function ($i) use ($npsn, $tahun_monev) {
            $tombol = ' <a href="javascript:void(0)" onclick="monev(\'' . $i->npsn . '\',\'' . $tahun_monev . '\')" class="btn btn-outline-primary">Monev</a>';
            if (in_array($i->npsn, $npsn)) {
              $tombol .= ' <a href="javascript:void(0)" onclick="cetak(\'' . $i->npsn . '\',\'' . $tahun_monev . '\')" class="btn btn-outline-success">Detail Monev</a>';
            }
            return $tombol;
          })
          ->addColumn('status_monev', function ($i) use ($npsn) {
            if (in_array($i->npsn, $npsn)) {
              return '<a href="javascript:void(0)" class="badge badge-success">Sudah</a>';
            }
          })
          ->orderColumn('status_monev', function ($w, $order) use ($npsn) {
            $list = implode("','", $npsn);

            $w->orderByRaw("
              CASE
                  WHEN s.npsn IN ('$list') THEN 1
                  ELSE 0
              END $order
            ");
          })
          ->addColumn('no_izin_operasional', function ($i) use ($ijop) {
            return $ijop[$i->npsn]->no_izin_operasional ?? '';
          })
          ->filterColumn('no_izin_operasional', function ($query, $keyword) use ($ijop) {
            // ambil semua npsn yang cocok
            $filteredNpsn = collect($ijop)
              ->filter(function ($item) use ($keyword) {
                return str_contains(strtolower($item->no_izin_operasional ?? ''), strtolower($keyword));
              })
              ->keys()
              ->toArray();

            // filter query utama berdasarkan npsn
            $query->whereIn('s.npsn', $filteredNpsn);
          })
          ->orderColumn('no_izin_operasional', function ($query, $order) use ($ijop) {
            // ambil urutan npsn berdasarkan sorting PHP
            $sorted = collect($ijop)
              ->keys()
              ->toArray();

            // pakai FIELD() biar urut sesuai array
            $query->orderByRaw("CASE WHEN s.npsn IN('" . implode("','", $sorted) . "') THEN 1 ELSE 0 END $order");
          })
          ->addColumn('selesai_berlaku', function ($i) use ($ijop) {
            return $ijop[$i->npsn]->selesai_berlaku ?? '';
          })
          ->filterColumn('selesai_berlaku', function ($query, $keyword) use ($ijop) {
            // ambil semua npsn yang cocok
            $filteredNpsn = collect($ijop)
              ->filter(function ($item) use ($keyword) {
                return str_contains(strtolower($item->selesai_berlaku ?? ''), strtolower($keyword));
              })
              ->keys()
              ->toArray();

            // filter query utama berdasarkan npsn
            $query->whereIn('s.npsn', $filteredNpsn);
          })
          ->orderColumn('selesai_berlaku', function ($query, $order) use ($ijop) {
            // ambil urutan npsn berdasarkan sorting PHP
            $sorted = collect($ijop)
              ->keys()
              ->toArray();

            // pakai FIELD() biar urut sesuai array
            $query->orderByRaw("CASE WHEN s.npsn IN('" . implode("','", $sorted) . "') THEN 1 ELSE 0 END $order");
          })
          ->rawColumns(['aksi', 'status_monev'])
          ->make(true);
        break;

      case 'form_monev':
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $monev = DB::table('cutoff_rekap.monitoring_ijop as jp')
          ->where('tahun', $tahun)
          ->where('npsn', $npsn)
          ->first();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
          ->where('s.npsn', $npsn)->first();

        $data = [
          'monev' => $monev,
          'sekolah' => $sekolah,
          'tahun' => $tahun,
        ];

        $content =  view('monev_baru.ijop.modal_form', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        $npsn = $request->npsn; //: "13900",
        $tahun = $request->tahun; //: "2026",
        $petugas = $request->petugas; //: "Sujianti",
        $tanggal = $request->tanggal; //: "2026-04-10",
        $hasil = $request->hasil; //: "Tindak Lanjut",
        $keterangan = $request->keterangan; //: "Lanjut sahaja"

        $data_simpan = [
          'tahun' => $tahun,
          'npsn' => $npsn,
          'petugas' => $petugas,
          'tingkat' => Auth::getUser()->tingkat,
          'id_kecamatan' => Auth::getUser()->id_kecamatan,
          'id_kelurahan' => Auth::getUser()->id_kelurahan,
          'user_id' => Auth::getUser()->id,
          'tanggal' => $tanggal,
          'hasil' => $hasil,
          'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($hasil == 'Tindak Lanjut') {
          $data_simpan = array_merge($data_simpan, ['keterangan' => $keterangan]);
        } else if ($hasil == 'Tutup') {
          if ($request->hasFile('pernyataan_tutup')) {
            $file = $request->file('pernyataan_tutup');

            $a = Bantuan::validate_file($request->file('pernyataan_tutup'), 'gambar');

            if ($a) {
              $nama_file = "file-monitoring/ijop_" . date('YmdHis') . "-" . uniqid() . "." . $file->extension();

              Storage::put($nama_file, file_get_contents($file));

              $data_simpan = array_merge($data_simpan, ['path_pernyataan_tutup' => $nama_file]);
            }
          }
        }

        $simpan = DB::table('cutoff_rekap.monitoring_ijop')->updateOrInsert(['npsn' => $npsn, 'tahun' => $tahun], $data_simpan);
        if ($simpan) {
          return ['code' => '200'];
        } else {
          return ['code' => '250'];
        }
        break;

      case 'cetak':
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $monev = DB::table('cutoff_rekap.monitoring_ijop as jp')
          ->where('tahun', $tahun)
          ->where('npsn', $npsn)
          ->first();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
          ->where('s.npsn', $npsn)->first();

        $data = [
          'monev' => $monev,
          'sekolah' => $sekolah,
          'tahun' => $tahun,
        ];

        $content =  view('monev_baru.ijop.cetak', $data)->render();
        return ['content' => $content];
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
