<?php

namespace App\Http\Controllers\Paudmutu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Monev\MasterController;

use DB, Auth, DataTables;

class PaudmutuController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::connection('pgsql_paud')->table('paud_bermutu.data_pendaftar')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);
        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];

        return view('paudmutu.main', $data);
        break;

      case 'get_data':
        // return $request->all();
        $kecamatan = $request->kecamatan;
        $kelurahan = $request->kelurahan;
        $status_monev = $request->status_monev;
        $tahun_monev = $request->tahun_monev;

        $riwayat_berkas_pendaftar = DB::connection('pgsql_paud')->table('paud_bermutu.data_pendaftar as d')
          ->leftJoin('paud_bermutu.berkas_pendaftar as b', 'b.pendaftar_id', 'd.id')
          ->leftJoin('paud_bermutu.riwayat_verifikasi as r', 'r.berkas_pendaftar_id', 'b.id')
          ->selectRaw("
            DISTINCT ON (b.id)
            d.id as id_pendaftar,
            b.id,
            r.status_berkas")
          ->where('d.tahun', $tahun_monev)
          ->whereNotNull('r.id')
          ->orderBy('b.id')
          ->orderByDesc('r.created_at');

        $riwayat = DB::connection('pgsql_paud')
          ->query()
          ->fromSub($riwayat_berkas_pendaftar, 'x')
          ->selectRaw("
            id_pendaftar,
            COUNT(*) as jumlah_berkas,
            COUNT(*) FILTER (WHERE status_berkas = 'Disetujui') AS disetujui,
            COUNT(*) FILTER (WHERE status_berkas = 'Pending') AS pending,
            COUNT(*) FILTER (WHERE status_berkas = 'Ditolak') AS ditolak
            ")
          ->groupBy('id_pendaftar');

        $jumlah_jenis_berkas = DB::connection('pgsql_paud')->table('paud_bermutu.jenis_berkas')
          ->where('tahun', $tahun_monev)->count();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as sek')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'sek.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'sek.desa')
          ->join('public.master_user as u', 'u.username', 'sek.npsn')
          ->join('paud_bermutu.data_pendaftar as d', 'd.npsn', 'sek.npsn')
          ->leftJoinSub($riwayat, 'b', function ($j) {
            return $j->on('d.id', '=', 'b.id_pendaftar');
          })
          ->when($kecamatan !== 'all', function ($w) use ($kecamatan) {
            return $w->whereRaw("kec.kecamatan_dispenduk ilike '%$kecamatan%'");
          })
          ->when($kelurahan !== 'all', function ($w) use ($kelurahan) {
            return $w->whereRaw("kel.kelurahan_dispenduk ilike '%$kelurahan%'");
          })
          ->when($status_monev !== 'all', function ($w) use ($status_monev, $jumlah_jenis_berkas) {
            if ($status_monev == 'Sudah') {
              $w->where('disetujui', $jumlah_jenis_berkas);
            } else {
              $w->where('disetujui', '!=', $jumlah_jenis_berkas);
            }
            // return $w->whereRaw("kel.status_monev_dispenduk ilike '%$status_monev%'");
          })
          ->when($tahun_monev !== 'all', function ($w) use ($tahun_monev) {
            return $w->whereRaw("d.tahun='$tahun_monev'");
          })
          ->whereIn('sek.jenjang', ['TK', 'KB', 'TPA', 'SPS', 'RA'])
          ->where('u.user_enable', true)
          ->selectRaw("
            sek.npsn,
            sek.jenjang,
            sek.status,
            sek.alamat,
            sek.nama,
            kec.kecamatan_dispenduk,
            kel.kelurahan_dispenduk,
            b.jumlah_berkas,
            b.disetujui,
            b.pending,
            b.ditolak
            ");

        return DataTables::queryBuilder($sekolah)
          ->addIndexColumn()
          ->addColumn('aksi', function ($r) use ($tahun_monev) {
            $html = '';
            if (!in_array(Auth::getUser()->tingkat, ['kecamatan', 'kelurahan'])) {
              $html = '<a href="' . route('paud_mutu-paudmutu', ['halaman' => 'halaman_verifikasi', 'tahun' => $tahun_monev, 'npsn' => $r->npsn]) . '" target="_blank" class="btn btn-sm btn-success">Verifikasi</a>';
            }
            return $html;
          })
          ->orderColumn('jumlah_berkas', function ($a, $sort) {
            return $a->orderByRaw("CASE WHEN jumlah_berkas IS NULL THEN 0 ELSE jumlah_berkas END $sort");
          })
          ->orderColumn('disetujui', function ($a, $sort) {
            return $a->orderByRaw("CASE WHEN disetujui IS NULL THEN 0 ELSE disetujui END $sort");
          })
          ->orderColumn('pending', function ($a, $sort) {
            return $a->orderByRaw("CASE WHEN pending IS NULL THEN 0 ELSE pending END $sort");
          })
          ->orderColumn('ditolak', function ($a, $sort) {
            return $a->orderByRaw("CASE WHEN ditolak IS NULL THEN 0 ELSE ditolak END $sort");
          })
          ->rawColumns(['aksi'])
          ->make(true);
        break;

      case 'halaman_verifikasi':
        $tahun = $request->tahun;
        $npsn = $request->npsn;

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->leftJoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->leftJoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->leftJoin('public.pegawai as p', function ($j) {
            $j->on('p.npsn', '=', 's.npsn')
              ->where('p.keterangan', 'Aktif')
              ->where('p.jabatan', '2')
              ->limit(1);
          })
          ->selectRaw("s.nama,s.jenjang,s.status,s.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.rt,s.rw,s.email,s.telp,p.nama as nama_ks,p.telp as telp_ks,p.hp as hp_ks")
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
          ->selectRaw("r.id,b.id as id_berkas_pendaftar,b.nilai,r.created_at,r.status_berkas,r.created_by,r.catatan,b.jenis_berkas_id,b.url_berkas")
          ->where('p.npsn', $npsn)
          ->where('p.tahun', $tahun)
          ->where('p.status_pendaftar', 'Terdaftar')
          ->get();

        $jenis_berkas = DB::connection('pgsql_paud')->table('paud_bermutu.jenis_berkas as j')
          ->where('j.tahun', date('Y'))
          ->get();

        $jenis_berkas = collect($jenis_berkas)->map(function ($j) use ($berkas_pendaftar) {
          $riwayat = collect($berkas_pendaftar)->where('jenis_berkas_id', $j->id)
            ->map(function ($r) {
              $r->created_at = date('d M Y H:i', strtotime($r->created_at ?? '')) ?? '';
              return $r;
            })->sortByDesc('created_at')->values();
          $terakhir = collect($berkas_pendaftar)->where('jenis_berkas_id', $j->id)->sortByDesc('created_at')->first();
          $j->berkas_pendaftar_id = $terakhir->id_berkas_pendaftar ?? '';
          $j->lastUpdated = date('d M Y H:i', strtotime($terakhir->created_at ?? '')) ?? '';
          $j->status_berkas = $terakhir->status_berkas ?? 'Belum ada';
          $j->url = $terakhir->url_berkas ?? '';
          $j->score = $terakhir->nilai ?? '';
          $j->catatan = $terakhir->catatan ?? '';
          $j->riwayat = $riwayat;
          return $j;
        })->values();

        $jenis_berkas = collect($jenis_berkas)->where('url', '!=', '')->values();

        $group_berkas = collect($jenis_berkas)->map(function ($o) {
          return [
            'group' => $o->group
          ];
        })->unique()->values();

        $data = [
          'active_menu' => 'paudmutu',
          'sekolah' => $sekolah,
          'paud_mutu' => $paud_mutu,
          'jenis_berkas' => $jenis_berkas,
          'group_berkas' => $group_berkas,
          'npsn' => $npsn,
          'tahun' => $tahun,
        ];
        return view('paudmutu.paudmutu.main', $data);
        break;

      case 'modal_berkas':
        $tahun = $request->tahun;
        $npsn = $request->npsn;
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
          'tahun' => $tahun,
          'npsn' => $npsn,
        ];


        if (empty($paud_mutu)) {
          return ['code' => '403'];
        }

        $content = view('paudmutu.paudmutu.modal_berkas', $data)->render();
        return ['code' => 200, 'content' => $content];
        break;

      case 'simpan_berkas':
        // return $request->all();
        $berkas_id = $request->berkas_id ?? ''; //"6"
        $catatan = $request->notes ?? ''; //"asdasd"
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
          'created_by' => Auth::getUser()->nama ?? '',
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
