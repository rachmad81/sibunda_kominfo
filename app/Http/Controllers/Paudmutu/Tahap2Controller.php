<?php

namespace App\Http\Controllers\Paudmutu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Session;

class Tahap2Controller extends Controller
{
  function halaman(Request $request)
  {
    date_default_timezone_set('Asia/Jakarta');
    $halaman = $request->halaman;
    $npsn = $request->npsn;
    $tahun = $request->tahun;

    switch ($halaman) {
      case 'main':
        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->leftJoin('public.pegawai as p', function ($j) {
            $j->on('p.npsn', '=', 's.npsn')
              ->where('p.keterangan', 'Aktif')
              ->where('p.jabatan', '2')
              ->limit(1);
          })
          ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.rt,s.rw,s.email,s.telp,p.nama as nama_ks,p.telp as telp_ks,p.hp as hp_ks,s.no_sk_operasional")
          ->where('s.npsn', $npsn)
          ->first();

        $paud_mutu = DB::connection('pgsql_paud')->table('paud_bermutu.data_pendaftar')
          ->where('npsn', $npsn)
          ->where('tahun', $tahun)
          ->where('status_pendaftar', 'Terdaftar')
          ->first();

        $berkas_pendaftar = DB::connection('pgsql_paud')->table('paud_bermutu.berkas_pendaftar as b')
          ->leftJoin('paud_bermutu.riwayat_verifikasi as r', 'r.berkas_pendaftar_id', 'b.id')
          ->join('paud_bermutu.data_pendaftar as p', 'p.id', 'b.pendaftar_id')
          ->selectRaw("r.id,b.id as id_berkas_pendaftar,b.nilai,r.created_at,r.status_berkas,r.created_by,r.catatan,b.jenis_berkas_id,b.url_berkas,b.narasi")
          ->where('p.npsn', $npsn)
          ->where('p.tahun', $tahun)
          ->where('p.status_pendaftar', 'Terdaftar')
          ->get();

        $jenis_berkas = DB::connection('pgsql_paud')->table('paud_bermutu.jenis_berkas as j')
          ->where('j.tahun', date('Y'))
          ->whereIn('group', ['Tahap II'])
          ->get();

        $jenis_berkas = collect($jenis_berkas)->map(function ($j) use ($berkas_pendaftar) {
          $riwayat = collect($berkas_pendaftar)->where('jenis_berkas_id', $j->id)
            ->map(function ($r) {
              $r->created_at = date('d M Y H:i', strtotime($r->created_at ?? '')) ?? '';
              return $r;
            })
            ->sortByDesc('created_at')->values();
          $terakhir = collect($berkas_pendaftar)->where('jenis_berkas_id', $j->id)->sortByDesc('created_at')->first();
          $j->berkas_pendaftar_id = $terakhir->id_berkas_pendaftar ?? '';
          $j->lastUpdated = date('d M Y H:i', strtotime($terakhir->created_at ?? '')) ?? '';
          $j->status_berkas = $terakhir->status_berkas ?? 'Belum ada';
          $j->url = $terakhir->url_berkas ?? '';
          $j->score = $terakhir->nilai ?? '';
          $j->catatan = $terakhir->catatan ?? '';
          $j->riwayat = $riwayat;
          $j->narasi = $terakhir->narasi ?? '';
          return $j;
        })->values();

        $group_berkas = collect($jenis_berkas)->map(function ($o) {
          return [
            'group' => $o->group
          ];
        })->unique()->values();

        $data = [
          'active_menu' => 'tahap2',
          'sekolah' => $sekolah,
          'paud_mutu' => $paud_mutu,
          'jenis_berkas' => $jenis_berkas,
          'group_berkas' => $group_berkas,
          'npsn' => $npsn,
          'tahun' => $tahun,
        ];
        return view('paudmutu.tahap2.main', $data);
        break;

      case 'modal_berkas':
        $id_jenis_berkas = $request->id_jenis;
        $jenis_berkas = DB::connection('pgsql_paud')->table('paud_bermutu.jenis_berkas as j')
          ->where('id', $id_jenis_berkas)
          ->first();

        $paud_mutu = DB::connection('pgsql_paud')->table('paud_bermutu.data_pendaftar')
          ->where('npsn', $npsn)
          ->where('tahun', $tahun)
          ->where('status_pendaftar', 'Terdaftar')
          ->first();

        $data_verifikasi = DB::connection('pgsql_paud')->table('paud_bermutu.riwayat_verifikasi as r')
          ->join('paud_bermutu.berkas_pendaftar as b', 'b.id', 'r.berkas_pendaftar_id')
          ->where('b.jenis_berkas_id', $jenis_berkas->id ?? '')
          ->where('b.pendaftar_id', $paud_mutu->id ?? '')
          ->selectRaw("b.url_berkas,r.status_berkas as status,r.catatan,r.created_at,r.created_by")
          ->orderByRaw("r.created_at DESC")
          ->get();

        $data_berkas = collect($data_verifikasi)->last();

        $riwayat_verifikasi = collect($data_verifikasi)->map(function ($i) {
          return (object)[
            'status' => $i->status ?? '',
            'catatan' => $i->catatan ?? '',
            'tanggal' => date('d M Y H:i', strtotime($i->created_at ?? '')) . ' WIB',
            'created_by' => $i->created_by
          ];
        });

        $data = [
          'jenis' => $jenis_berkas,
          'pendaftar' => $paud_mutu,
          'riwayat_verifikasi' => $riwayat_verifikasi,
          'data_berkas' => $data_berkas,
        ];


        if (empty($paud_mutu)) {
          return ['code' => '403'];
        }

        $content = view('paudmutu.tahap2.modal_berkas', $data)->render();
        return ['code' => 200, 'content' => $content];
        break;

      case 'pendaftaran':
        $data_simpan = [
          'npsn' => $npsn,
          'tahun' => $tahun,
          'updated_at' => date('Y-m-d H:i:s'),
          'status_pendaftar' => 'Terdaftar',
        ];
        $simpan = DB::connection('pgsql_paud')->table('paud_bermutu.data_pendaftar')
          ->updateOrInsert(['npsn' => $npsn, 'tahun' => $tahun], $data_simpan);

        if ($simpan) {
          return ['code' => 200];
        } else {
          return ['code' => 250];
        }
        break;

      case 'simpan_berkas':
        // return $request->all();
        $berkas_id = $request->berkas_id ?? ''; //"6"
        $catatan = $request->notes ?? ''; //"asdasd"
        $narasi = $request->dampak ?? ''; //"asdasd"
        $pendaftar_id = $request->pendaftar_id ?? ''; //"1"
        $status = $request->status ?? ''; //"Pending"
        $url_berkas = $request->url ?? ''; //"https://contoh-website.com"
        $score = $request->score ?? 0; //"https://contoh-website.com"

        $cari_berkas = DB::connection('pgsql_paud')->table('paud_bermutu.berkas_pendaftar')
          ->where('pendaftar_id', $pendaftar_id)
          ->where('jenis_berkas_id', $berkas_id)
          ->first();

        $data_simpan = [
          'url_berkas' => $url_berkas,
          'jenis_berkas_id' => $berkas_id,
          'pendaftar_id' => $pendaftar_id,
          'narasi' => $narasi,
          'updated_at' => date('Y-m-d H:i:s'),
          'nilai' => $status == 'Ditolak' ? 0 : $score,
        ];

        if (!empty($cari_berkas)) {
          $simpan = DB::connection('pgsql_paud')->table('paud_bermutu.berkas_pendaftar')
            ->where('pendaftar_id', $pendaftar_id)
            ->where('jenis_berkas_id', $berkas_id)
            ->update($data_simpan);

          $id_berkas_pendaftar = $cari_berkas->id;
        } else {
          $simpan = DB::connection('pgsql_paud')->table('paud_bermutu.berkas_pendaftar')
            ->insertGetId($data_simpan, 'id');
          $id_berkas_pendaftar = $simpan;
        }

        $data_verifikasi = [
          'catatan' => $catatan,
          'status_berkas' => $status,
          'created_by' => 'Sekolah',
          'berkas_pendaftar_id' => $id_berkas_pendaftar,
          'updated_at' => date('Y-m-d H:i:s'),
        ];
        $simpan = DB::connection('pgsql_paud')->table('paud_bermutu.riwayat_verifikasi')
          ->insert($data_verifikasi);

        if ($simpan) {
          return ['code' => 200];
        } else {
          return ['code' => 250];
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
