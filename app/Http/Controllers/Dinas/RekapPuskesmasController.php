<?php

namespace App\Http\Controllers\Dinas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, DataTables, Carbon\Carbon;

class RekapPuskesmasController extends Controller
{
  // Nilai jenjang yang dipakai di rekap ini.
  // 'SPS' ditampilkan sebagai "PPT" (istilah Surabaya), tapi filter tetap pakai nilai 'SPS'.
  const JENJANG = ['TK', 'KB', 'TPA', 'SPS', 'RA'];
  const LABEL_JENJANG = ['TK' => 'TK', 'KB' => 'KB', 'TPA' => 'TPA', 'SPS' => 'PPT', 'RA' => 'RA'];
  const LABEL_STATUS = ['akan' => 'Akan Diperiksa', 'sudah' => 'Sudah Diperiksa'];
  // Tahun paling awal yang bisa dipilih di rekap Per Bulan.
  const TAHUN_AWAL = 2026;
  const NAMA_BULAN = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember',
  ];

  function halaman(Request $request)
  {
    $halaman = $request->halaman;
    $tahunList = range((int) date('Y'), self::TAHUN_AWAL);
    // Default "Sampai Tanggal" = akhir bulan berjalan (Bulan/Tahun sama-sama default ke
    // bulan ini), jadi kalau gak disentuh, hasilnya sama kayak gak ada cutoff sama sekali.
    $tanggalDefault = date('Y-m-t');

    switch ($halaman) {
      case 'wilayah':
        return view('dinas.rekap_puskesmas.wilayah', [
          'kecamatan' => DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get(),
          'label_jenjang' => self::LABEL_JENJANG,
          'jenjang_key' => array_map(fn($j) => strtolower($j), self::JENJANG),
          'tahun_list' => $tahunList,
          'tanggal_default' => $tanggalDefault,
        ]);

      case 'get_data_wilayah':
        return $this->getDataWilayah($request);

      case 'get_footer_wilayah':
        return $this->getFooterWilayah($request);

      case 'tanggal':
        return view('dinas.rekap_puskesmas.tanggal', [
          'kecamatan' => DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get(),
          'label_jenjang' => self::LABEL_JENJANG,
          'jenjang_key' => array_map(fn($j) => strtolower($j), self::JENJANG),
          'tahun_list' => $tahunList,
          'tanggal_default' => $tanggalDefault,
        ]);

      case 'get_data_tanggal':
        return $this->getDataTanggal($request);

      case 'get_footer_tanggal':
        return $this->getFooterTanggal($request);

      case 'bulan':
        return view('dinas.rekap_puskesmas.bulan', [
          'kecamatan' => DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get(),
          'tahun_list' => $tahunList,
          'label_jenjang' => self::LABEL_JENJANG,
          'jenjang_key' => array_map(fn($j) => strtolower($j), self::JENJANG),
          'tanggal_default' => $tanggalDefault,
        ]);

      case 'get_data_bulan':
        return $this->getDataBulan($request);

      case 'get_footer_bulan':
        return $this->getFooterBulan($request);

      case 'kunjungan':
        return view('dinas.rekap_puskesmas.kunjungan', [
          'kecamatan' => DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get(),
          'label_jenjang' => self::LABEL_JENJANG,
          'tahun_list' => $tahunList,
          'tanggal_default' => $tanggalDefault,
        ]);

      case 'get_data_kunjungan':
        return $this->getDataKunjungan($request);

      case 'get_footer_kunjungan':
        return $this->getFooterKunjungan($request);

      case 'modal_nama':
        return $this->modalNama($request);

      default:
        return '404 - Halaman tidak ditemukan';
    }
  }

  /**
   * Query dasar: satu baris = satu siswa peserta kunjungan (snapshot aktif),
   * sudah di-join ke kunjungan, sekolah, kecamatan & kelurahan.
   */
  private function basePesertaQuery()
  {
    return DB::connection('pgsql_paud')->table('kegiatan_pendukung.kunjungan_puskesmas_peserta as p')
      ->join('kegiatan_pendukung.kunjungan_puskesmas as k', 'k.id', '=', 'p.kunjungan_puskesmas_id')
      ->join('public.sekolah as se', 'se.npsn', '=', 'k.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', '=', 'se.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', '=', 'se.desa')
      ->whereNull('p.deleted_at')
      ->whereNull('k.deleted_at')
      ->whereIn('se.jenjang', self::JENJANG);
  }

  private function filterKecamatanKelurahan($query, Request $request)
  {
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    return $query
      ->when($kecamatan_kode && $kecamatan_kode != 'All', fn($w) => $w->where('kec.kecamatan_kode', $kecamatan_kode))
      ->when($kelurahan_kode && $kelurahan_kode != 'All', fn($w) => $w->where('kel.kelurahan_kode', $kelurahan_kode));
  }

  // $request->tanggal ("Sampai Tanggal") itu cutoff titik-waktu: kunjungan dengan
  // k.tanggal SETELAH tanggal ini dianggap belum terjadi. Ini bukan cuma nyaring baris
  // yang tampil, tapi juga ikut membatasi jendela buat logic "status terbaru per siswa"
  // (latestPesertaSub) - makanya taruh di sini, satu tempat, biar konsisten ke semua yang
  // pakai filterBulanTahun().
  private function filterBulanTahun($query, Request $request)
  {
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $tanggal = $request->tanggal;

    return $query
      ->when($bulan && $bulan != 'semua', fn($w) => $w->whereRaw("to_char(k.tanggal, 'MM') = ?", [$bulan]))
      ->when($tahun && $tahun != 'semua', fn($w) => $w->whereRaw('EXTRACT(YEAR FROM k.tanggal) = ?', [$tahun]))
      ->when($tanggal, fn($w) => $w->whereDate('k.tanggal', '<=', $tanggal));
  }

  private function filterJenjang($query, Request $request)
  {
    $jenjang = $request->jenjang;

    return $query->when($jenjang && $jenjang != 'semua', fn($w) => $w->where('se.jenjang', $jenjang));
  }

  // Kolom "Total" (jenjang null) di tabel pivot: kalau ada filter Jenjang lagi aktif,
  // angkanya udah otomatis kepotong ke jenjang itu doang - drill-downnya juga harus
  // ikut kepotong sama, bukan malah nunjukin semua jenjang.
  private function jenjangUntukParam($jenjang, Request $request)
  {
    if ($jenjang) {
      return $jenjang;
    }
    return $request->jenjang && $request->jenjang != 'semua' ? $request->jenjang : null;
  }

  /**
   * Satu siswa (siswa_id) bisa ke-snapshot lebih dari sekali kalau sekolahnya bikin
   * lebih dari satu jadwal kunjungan (mis. tgl 14 sudah diperiksa, lalu di-jadwal ulang
   * tgl 20 jadi "akan diperiksa" lagi). Supaya status yang dipakai adalah yang PALING
   * BARU, subquery ini ambil satu baris per siswa_id (DISTINCT ON), diurutkan dari
   * tanggal kunjungan terbaru. $filterFn (opsional) buat filter kecamatan/kelurahan/
   * jenjang/bulan/tahun SEBELUM diambil yang terbarunya.
   *
   * TIDAK dipakai di rekap Per Tanggal - di situ semua jadwal memang harus tampil apa
   * adanya per tanggal, bukan cuma status terbarunya.
   */
  private function latestPesertaSub(callable $filterFn = null)
  {
    $q = $this->basePesertaQuery();
    if ($filterFn) {
      $q = $filterFn($q);
    }

    return $q->selectRaw("DISTINCT ON (p.siswa_id)
        p.siswa_id, p.nama, p.jenis_kelamin, p.usia, p.sudah_diperiksa,
        se.jenjang, se.nama as nama_sekolah,
        kec.kecamatan_kode, kec.kecamatan_dispenduk,
        kel.kelurahan_kode, kel.kelurahan_dispenduk,
        k.id as kunjungan_id, k.tanggal, k.npsn, k.nama_puskesmas, k.nama_posyandu")
      ->orderBy('p.siswa_id')
      ->orderBy('k.tanggal', 'desc')
      ->orderBy('k.id', 'desc');
  }

  private function latestPesertaTable(callable $filterFn = null)
  {
    return DB::connection('pgsql_paud')->query()->fromSub($this->latestPesertaSub($filterFn), 'lp');
  }

  /**
   * Select fragment jumlah murid (unik per siswa) per jenjang x status, plus 3 kolom
   * total (semua jenjang digabung). $siswaCol/$jenjangCol/$statusCol disesuaikan
   * tergantung query-nya langsung dari basePesertaQuery() (alias p./se.) atau dari
   * hasil latestPesertaTable() (alias lp.).
   */
  private function jenjangSelectFields($siswaCol = 'p.siswa_id', $jenjangCol = 'se.jenjang', $statusCol = 'p.sudah_diperiksa')
  {
    $fields = [];
    foreach (self::JENJANG as $j) {
      $key = strtolower($j);
      $fields[] = DB::raw("COUNT(DISTINCT {$siswaCol}) FILTER (WHERE {$jenjangCol} = '{$j}') as {$key}_jumlah");
      $fields[] = DB::raw("COUNT(DISTINCT {$siswaCol}) FILTER (WHERE {$jenjangCol} = '{$j}' AND NOT {$statusCol}) as {$key}_akan");
      $fields[] = DB::raw("COUNT(DISTINCT {$siswaCol}) FILTER (WHERE {$jenjangCol} = '{$j}' AND {$statusCol}) as {$key}_sudah");
    }
    $fields[] = DB::raw("COUNT(DISTINCT {$siswaCol}) as total_jumlah");
    $fields[] = DB::raw("COUNT(DISTINCT {$siswaCol}) FILTER (WHERE NOT {$statusCol}) as total_akan");
    $fields[] = DB::raw("COUNT(DISTINCT {$siswaCol}) FILTER (WHERE {$statusCol}) as total_sudah");

    return $fields;
  }

  private function renderLink($jumlah, $param)
  {
    if (!$jumlah) {
      return 0;
    }

    $p = base64_encode(json_encode($param));

    return '<a href="javascript:void(0)" onclick="modal_nama(\'' . $p . '\')">' . number_format($jumlah, 0, ',', '.') . '</a>';
  }

  /**
   * Pasang 5 x 3 kolom jenjang (jumlah/akan/sudah) + 3 kolom total ke DataTables,
   * masing-masing jadi link drill-down nama. $paramFn($row, $jenjang, $status) => array param.
   * $jenjang null = kolom total (semua jenjang), $status null = jumlah (akan + sudah).
   */
  private function attachJenjangColumns($dt, callable $paramFn)
  {
    $statusList = ['jumlah' => null, 'akan' => 'akan', 'sudah' => 'sudah'];
    $rawColumns = [];

    foreach (self::JENJANG as $j) {
      $key = strtolower($j);
      foreach ($statusList as $suffix => $status) {
        $col = "{$key}_{$suffix}";
        $rawColumns[] = $col;
        $dt->addColumn($col, function ($w) use ($col, $j, $status, $paramFn) {
          return $this->renderLink($w->$col ?? 0, $paramFn($w, $j, $status));
        });
      }
    }

    foreach ($statusList as $suffix => $status) {
      $col = "total_{$suffix}";
      $rawColumns[] = $col;
      $dt->addColumn($col, function ($w) use ($col, $status, $paramFn) {
        return $this->renderLink($w->$col ?? 0, $paramFn($w, null, $status));
      });
    }

    return $dt->rawColumns($rawColumns);
  }

  /**
   * Versi non-DataTables dari attachJenjangColumns, buat baris footer total
   * (satu baris hasil agregasi tanpa pagination).
   */
  private function footerRow($row, callable $paramFn)
  {
    $statusList = ['jumlah' => null, 'akan' => 'akan', 'sudah' => 'sudah'];
    $out = [];

    foreach (self::JENJANG as $j) {
      $key = strtolower($j);
      foreach ($statusList as $suffix => $status) {
        $col = "{$key}_{$suffix}";
        $out[$col] = $this->renderLink($row->$col ?? 0, $paramFn(null, $j, $status));
      }
    }

    foreach ($statusList as $suffix => $status) {
      $col = "total_{$suffix}";
      $out[$col] = $this->renderLink($row->$col ?? 0, $paramFn(null, null, $status));
    }

    return $out;
  }

  function getDataWilayah(Request $request)
  {
    $filter = function ($w) use ($request) {
      return $w
        ->tap(fn($x) => $this->filterKecamatanKelurahan($x, $request))
        ->tap(fn($x) => $this->filterBulanTahun($x, $request))
        ->tap(fn($x) => $this->filterJenjang($x, $request));
    };

    $data = $this->latestPesertaTable($filter)
      ->select(array_merge([
        'lp.kecamatan_kode',
        'lp.kecamatan_dispenduk',
        'lp.kelurahan_kode',
        'lp.kelurahan_dispenduk',
      ], $this->jenjangSelectFields('lp.siswa_id', 'lp.jenjang', 'lp.sudah_diperiksa')))
      ->groupBy('lp.kecamatan_kode', 'lp.kecamatan_dispenduk', 'lp.kelurahan_kode', 'lp.kelurahan_dispenduk')
      ->orderBy('lp.kecamatan_dispenduk')
      ->orderBy('lp.kelurahan_dispenduk')
      ->get();

    $dt = DataTables::of($data)->addIndexColumn();
    $this->attachJenjangColumns($dt, function ($w, $jenjang, $status) use ($request) {
      return [
        'sumber' => 'wilayah',
        'kecamatan_kode' => $w->kecamatan_kode,
        'kelurahan_kode' => $w->kelurahan_kode,
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
        'cutoff' => $request->tanggal,
        'jenjang' => $this->jenjangUntukParam($jenjang, $request),
        'status' => $status,
      ];
    });

    return $dt->make(true);
  }

  function getFooterWilayah(Request $request)
  {
    $filter = function ($w) use ($request) {
      return $w
        ->tap(fn($x) => $this->filterKecamatanKelurahan($x, $request))
        ->tap(fn($x) => $this->filterBulanTahun($x, $request))
        ->tap(fn($x) => $this->filterJenjang($x, $request));
    };

    $row = $this->latestPesertaTable($filter)
      ->select($this->jenjangSelectFields('lp.siswa_id', 'lp.jenjang', 'lp.sudah_diperiksa'))
      ->first();

    return $this->footerRow($row, fn($w, $jenjang, $status) => [
      'sumber' => 'wilayah',
      'kecamatan_kode' => $request->kecamatan_kode,
      'kelurahan_kode' => $request->kelurahan_kode,
      'bulan' => $request->bulan,
      'tahun' => $request->tahun,
      'cutoff' => $request->tanggal,
      'jenjang' => $this->jenjangUntukParam($jenjang, $request),
      'status' => $status,
    ]);
  }

  // Per Tanggal sengaja TIDAK pakai latestPesertaTable() - semua jadwal kunjungan
  // memang harus tampil apa adanya per tanggal, bukan cuma status terbaru per siswa.
  function getDataTanggal(Request $request)
  {
    $data = $this->basePesertaQuery()
      ->tap(fn($w) => $this->filterKecamatanKelurahan($w, $request))
      ->tap(fn($w) => $this->filterBulanTahun($w, $request))
      ->tap(fn($w) => $this->filterJenjang($w, $request))
      ->select(array_merge([
        'k.tanggal',
        'kec.kecamatan_kode',
        'kec.kecamatan_dispenduk',
        'kel.kelurahan_kode',
        'kel.kelurahan_dispenduk',
      ], $this->jenjangSelectFields()))
      ->groupBy('k.tanggal', 'kec.kecamatan_kode', 'kec.kecamatan_dispenduk', 'kel.kelurahan_kode', 'kel.kelurahan_dispenduk')
      ->orderBy('k.tanggal')
      ->orderBy('kec.kecamatan_dispenduk')
      ->orderBy('kel.kelurahan_dispenduk')
      ->get();

    $dt = DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('bulan', fn($w) => self::NAMA_BULAN[Carbon::parse($w->tanggal)->format('m')] ?? '-')
      ->addColumn('tanggal_fmt', fn($w) => Carbon::parse($w->tanggal)->format('d-m-Y'));

    $this->attachJenjangColumns($dt, function ($w, $jenjang, $status) use ($request) {
      return [
        'sumber' => 'tanggal',
        'kecamatan_kode' => $w->kecamatan_kode,
        'kelurahan_kode' => $w->kelurahan_kode,
        'tanggal' => $w->tanggal,
        'cutoff' => $request->tanggal,
        'jenjang' => $this->jenjangUntukParam($jenjang, $request),
        'status' => $status,
      ];
    });

    return $dt->make(true);
  }

  // Footer Per Tanggal ngerangkum SEMUA tanggal dalam window bulan/tahun yang difilter,
  // jadi satu siswa bisa nyangkut di lebih dari satu tanggal - di sini WAJIB pakai status
  // terbaru (latestPesertaTable), beda dari baris per-tanggalnya sendiri yang apa adanya.
  function getFooterTanggal(Request $request)
  {
    $filter = function ($w) use ($request) {
      return $w
        ->tap(fn($x) => $this->filterKecamatanKelurahan($x, $request))
        ->tap(fn($x) => $this->filterBulanTahun($x, $request))
        ->tap(fn($x) => $this->filterJenjang($x, $request));
    };

    $row = $this->latestPesertaTable($filter)
      ->select($this->jenjangSelectFields('lp.siswa_id', 'lp.jenjang', 'lp.sudah_diperiksa'))
      ->first();

    return $this->footerRow($row, fn($w, $jenjang, $status) => [
      'sumber' => 'tanggal_footer',
      'kecamatan_kode' => $request->kecamatan_kode,
      'kelurahan_kode' => $request->kelurahan_kode,
      'bulan' => $request->bulan,
      'tahun' => $request->tahun,
      'cutoff' => $request->tanggal,
      'jenjang' => $this->jenjangUntukParam($jenjang, $request),
      'status' => $status,
    ]);
  }

  function getDataBulan(Request $request)
  {
    $tahun = (int) ($request->tahun ?: date('Y'));
    if ($tahun < self::TAHUN_AWAL) {
      $tahun = self::TAHUN_AWAL;
    }

    // Bulan terbaru yang boleh ditampilkan: kalau tahun berjalan, batasnya bulan sekarang;
    // kalau tahun lampau, batasnya Desember.
    $bulan_max = ($tahun == (int) date('Y')) ? (int) date('n') : 12;

    // Satu siswa dedup PER BULAN (bukan per tahun): kalau ada 2 jadwal di bulan yang
    // sama, yang dipakai cuma yang tanggalnya paling baru di bulan itu.
    $sub = $this->basePesertaQuery()
      ->whereRaw('EXTRACT(YEAR FROM k.tanggal) = ?', [$tahun])
      ->tap(fn($w) => $this->filterKecamatanKelurahan($w, $request))
      ->tap(fn($w) => $this->filterJenjang($w, $request))
      ->when($request->tanggal, fn($w) => $w->whereDate('k.tanggal', '<=', $request->tanggal))
      ->selectRaw("DISTINCT ON (EXTRACT(MONTH FROM k.tanggal), p.siswa_id)
          EXTRACT(MONTH FROM k.tanggal)::int as bulan_angka,
          p.siswa_id, p.sudah_diperiksa, se.jenjang")
      ->orderByRaw('EXTRACT(MONTH FROM k.tanggal)')
      ->orderBy('p.siswa_id')
      ->orderBy('k.tanggal', 'desc')
      ->orderBy('k.id', 'desc');

    $raw = DB::connection('pgsql_paud')->query()->fromSub($sub, 'lp')
      ->select(array_merge(['lp.bulan_angka'], $this->jenjangSelectFields('lp.siswa_id', 'lp.jenjang', 'lp.sudah_diperiksa')))
      ->groupBy('lp.bulan_angka')
      ->get()
      ->keyBy('bulan_angka');

    $kosong = array_fill_keys(array_merge(
      collect(self::JENJANG)->flatMap(fn($j) => ["{$this->key($j)}_jumlah", "{$this->key($j)}_akan", "{$this->key($j)}_sudah"])->toArray(),
      ['total_jumlah', 'total_akan', 'total_sudah']
    ), 0);

    // Susun dari bulan terbaru turun ke Januari, isi 0 untuk bulan yang belum ada datanya.
    $data = collect();
    for ($b = $bulan_max; $b >= 1; $b--) {
      $row = $raw->get($b);
      $isi = $row ? (array) $row : $kosong;
      $data->push((object) array_merge($isi, [
        'tahun' => $tahun,
        'bulan_angka' => $b,
        'bulan' => self::NAMA_BULAN[str_pad($b, 2, '0', STR_PAD_LEFT)],
      ]));
    }

    $dt = DataTables::of($data)->addIndexColumn();
    $this->attachJenjangColumns($dt, function ($w, $jenjang, $status) use ($request) {
      return [
        'sumber' => 'bulan',
        'tahun' => $w->tahun,
        'bulan' => str_pad($w->bulan_angka, 2, '0', STR_PAD_LEFT),
        'kecamatan_kode' => $request->kecamatan_kode,
        'kelurahan_kode' => $request->kelurahan_kode,
        'cutoff' => $request->tanggal,
        'jenjang' => $this->jenjangUntukParam($jenjang, $request),
        'status' => $status,
      ];
    });

    return $dt->make(true);
  }

  function getFooterBulan(Request $request)
  {
    $tahun = (int) ($request->tahun ?: date('Y'));
    if ($tahun < self::TAHUN_AWAL) {
      $tahun = self::TAHUN_AWAL;
    }

    // Footer = total 1 tahun penuh, dedup per siswa (bukan per bulan) supaya siswa yang
    // pindah jadwal lintas bulan tetap dihitung sekali.
    $filter = function ($w) use ($tahun, $request) {
      return $w
        ->whereRaw('EXTRACT(YEAR FROM k.tanggal) = ?', [$tahun])
        ->tap(fn($x) => $this->filterKecamatanKelurahan($x, $request))
        ->tap(fn($x) => $this->filterJenjang($x, $request))
        ->when($request->tanggal, fn($x) => $x->whereDate('k.tanggal', '<=', $request->tanggal));
    };

    $row = $this->latestPesertaTable($filter)
      ->select($this->jenjangSelectFields('lp.siswa_id', 'lp.jenjang', 'lp.sudah_diperiksa'))
      ->first();

    return $this->footerRow($row, fn($w, $jenjang, $status) => [
      'sumber' => 'bulan',
      'tahun' => $tahun,
      'bulan' => null,
      'kecamatan_kode' => $request->kecamatan_kode,
      'kelurahan_kode' => $request->kelurahan_kode,
      'cutoff' => $request->tanggal,
      'jenjang' => $this->jenjangUntukParam($jenjang, $request),
      'status' => $status,
    ]);
  }

  private function key($jenjang)
  {
    return strtolower($jenjang);
  }

  private function filterKunjunganList($query, Request $request)
  {
    $jenjang = $request->jenjang;

    return $query
      ->tap(fn($w) => $this->filterKecamatanKelurahan($w, $request))
      ->tap(fn($w) => $this->filterBulanTahun($w, $request))
      ->when($jenjang && $jenjang != 'semua', fn($w) => $w->where('se.jenjang', $jenjang));
  }

  // Baris per-kunjungan tetap tampilkan angka literal snapshot kunjungan itu sendiri
  // (historis, apa adanya) - dedup "status terbaru" cuma dipakai di footer totalnya.
  function getDataKunjungan(Request $request)
  {
    $subPeserta = DB::connection('pgsql_paud')->table('kegiatan_pendukung.kunjungan_puskesmas_peserta as pp')
      ->select(
        'kunjungan_puskesmas_id',
        DB::raw('COUNT(DISTINCT siswa_id) as jumlah'),
        DB::raw('COUNT(DISTINCT siswa_id) FILTER (WHERE NOT sudah_diperiksa) as akan'),
        DB::raw('COUNT(DISTINCT siswa_id) FILTER (WHERE sudah_diperiksa) as sudah')
      )
      ->whereNull('deleted_at')
      ->groupBy('kunjungan_puskesmas_id');

    $data = DB::connection('pgsql_paud')->table('kegiatan_pendukung.kunjungan_puskesmas as k')
      ->join('public.sekolah as se', 'se.npsn', '=', 'k.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', '=', 'se.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', '=', 'se.desa')
      ->leftJoinSub($subPeserta, 'sp', 'sp.kunjungan_puskesmas_id', '=', 'k.id')
      ->whereNull('k.deleted_at')
      ->whereIn('se.jenjang', self::JENJANG)
      ->tap(fn($w) => $this->filterKunjunganList($w, $request))
      ->select([
        'k.id as kunjungan_id',
        'k.npsn',
        'se.nama as nama_sekolah',
        'se.jenjang',
        'kec.kecamatan_dispenduk',
        'kel.kelurahan_dispenduk',
        'k.nama_puskesmas',
        'k.nama_posyandu',
        'k.cp_puskesmas_nama',
        'k.cp_puskesmas_hp',
        'k.cp_posyandu_nama',
        'k.cp_posyandu_hp',
        'k.tanggal',
        DB::raw('COALESCE(sp.jumlah, 0) as jumlah_murid'),
        DB::raw('COALESCE(sp.akan, 0) as akan_diperiksa'),
        DB::raw('COALESCE(sp.sudah, 0) as sudah_diperiksa'),
      ])
      ->orderBy('k.tanggal', 'desc')
      ->get();

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('jenjang_label', fn($w) => self::LABEL_JENJANG[$w->jenjang] ?? $w->jenjang)
      ->addColumn('cp_puskesmas', fn($w) => trim($w->cp_puskesmas_nama . ($w->cp_puskesmas_hp ? ' (' . $w->cp_puskesmas_hp . ')' : '')))
      ->addColumn('cp_posyandu', fn($w) => trim($w->cp_posyandu_nama . ($w->cp_posyandu_hp ? ' (' . $w->cp_posyandu_hp . ')' : '')))
      ->addColumn('tanggal_fmt', fn($w) => $w->tanggal ? Carbon::parse($w->tanggal)->format('d-m-Y') : '-')
      ->addColumn('jumlah_murid', fn($w) => $this->renderLink($w->jumlah_murid, ['sumber' => 'kunjungan', 'kunjungan_id' => $w->kunjungan_id, 'status' => null]))
      ->addColumn('akan_diperiksa', fn($w) => $this->renderLink($w->akan_diperiksa, ['sumber' => 'kunjungan', 'kunjungan_id' => $w->kunjungan_id, 'status' => 'akan']))
      ->addColumn('sudah_diperiksa', fn($w) => $this->renderLink($w->sudah_diperiksa, ['sumber' => 'kunjungan', 'kunjungan_id' => $w->kunjungan_id, 'status' => 'sudah']))
      ->rawColumns(['cp_puskesmas', 'cp_posyandu', 'jumlah_murid', 'akan_diperiksa', 'sudah_diperiksa'])
      ->make(true);
  }

  function getFooterKunjungan(Request $request)
  {
    $row = $this->latestPesertaTable(fn($w) => $this->filterKunjunganList($w, $request))
      ->selectRaw('
        COUNT(DISTINCT lp.siswa_id) as jumlah_murid,
        COUNT(DISTINCT lp.siswa_id) FILTER (WHERE NOT lp.sudah_diperiksa) as akan_diperiksa,
        COUNT(DISTINCT lp.siswa_id) FILTER (WHERE lp.sudah_diperiksa) as sudah_diperiksa
      ')
      ->first();

    $extra = [
      'sumber' => 'kunjungan_footer',
      'kecamatan_kode' => $request->kecamatan_kode,
      'kelurahan_kode' => $request->kelurahan_kode,
      'jenjang' => $request->jenjang && $request->jenjang != 'semua' ? $request->jenjang : null,
      'bulan' => $request->bulan,
      'tahun' => $request->tahun,
      'cutoff' => $request->tanggal,
    ];

    return [
      'jumlah_murid' => $this->renderLink($row->jumlah_murid, array_merge($extra, ['status' => null])),
      'akan_diperiksa' => $this->renderLink($row->akan_diperiksa, array_merge($extra, ['status' => 'akan'])),
      'sudah_diperiksa' => $this->renderLink($row->sudah_diperiksa, array_merge($extra, ['status' => 'sudah'])),
    ];
  }

  /**
   * Modal drill-down daftar nama siswa, dipakai oleh semua rekap. Untuk semua sumber
   * KECUALI tanggal/tanggal_footer, daftarnya diambil dari status TERBARU per siswa
   * (latestPesertaTable) - jadi satu siswa gak muncul dobel dengan status beda.
   */
  private function modalNama(Request $request)
  {
    $param = json_decode(base64_decode($request->param));
    $sumber = $param->sumber ?? 'wilayah';
    $jenjang = $param->jenjang ?? null;
    $status = $param->status ?? null; // 'akan' | 'sudah' | null (keduanya)
    $kecamatan_kode = $param->kecamatan_kode ?? null;
    $kelurahan_kode = $param->kelurahan_kode ?? null;
    $tanggal = $param->tanggal ?? null; // dipakai sumber tanggal saja
    $bulan = $param->bulan ?? null;
    $tahun = $param->tahun ?? null;
    $cutoff = $param->cutoff ?? null; // "Sampai Tanggal" - beda dari $tanggal (tanggal exact di atas)
    $kunjungan_id = $param->kunjungan_id ?? null;

    if ($sumber == 'tanggal') {
      $query = $this->basePesertaQuery()
        ->when($kecamatan_kode && $kecamatan_kode != 'All', fn($w) => $w->where('kec.kecamatan_kode', $kecamatan_kode))
        ->when($kelurahan_kode && $kelurahan_kode != 'All', fn($w) => $w->where('kel.kelurahan_kode', $kelurahan_kode))
        ->when($jenjang, fn($w) => $w->where('se.jenjang', $jenjang))
        ->when($tanggal, fn($w) => $w->where('k.tanggal', $tanggal))
        ->when($bulan && $bulan != 'semua', fn($w) => $w->whereRaw("to_char(k.tanggal, 'MM') = ?", [$bulan]))
        ->when($tahun && $tahun != 'semua', fn($w) => $w->whereRaw('EXTRACT(YEAR FROM k.tanggal) = ?', [$tahun]))
        ->when($cutoff, fn($w) => $w->whereDate('k.tanggal', '<=', $cutoff));

      if ($status == 'akan') {
        $query->where('p.sudah_diperiksa', false);
      } elseif ($status == 'sudah') {
        $query->where('p.sudah_diperiksa', true);
      }

      $data = $query->select([
        'p.siswa_id',
        'p.nama',
        'p.jenis_kelamin',
        'p.usia',
        'k.npsn',
        'se.nama as nama_sekolah',
        'se.jenjang',
        'kel.kelurahan_dispenduk',
        'k.tanggal',
        'k.nama_puskesmas',
        'k.nama_posyandu',
        'p.sudah_diperiksa',
      ])
        ->orderBy('se.nama')
        ->orderBy('p.nama')
        ->get()
        ->unique('siswa_id')
        ->values();
    } else {
      $filter = function ($w) use ($kecamatan_kode, $kelurahan_kode, $jenjang, $kunjungan_id, $bulan, $tahun, $cutoff) {
        return $w
          ->when($kecamatan_kode && $kecamatan_kode != 'All', fn($x) => $x->where('kec.kecamatan_kode', $kecamatan_kode))
          ->when($kelurahan_kode && $kelurahan_kode != 'All', fn($x) => $x->where('kel.kelurahan_kode', $kelurahan_kode))
          ->when($jenjang, fn($x) => $x->where('se.jenjang', $jenjang))
          ->when($kunjungan_id, fn($x) => $x->where('k.id', $kunjungan_id))
          ->when($bulan && $bulan != 'semua', fn($x) => $x->whereRaw("to_char(k.tanggal, 'MM') = ?", [$bulan]))
          ->when($tahun && $tahun != 'semua', fn($x) => $x->whereRaw('EXTRACT(YEAR FROM k.tanggal) = ?', [$tahun]))
          ->when($cutoff, fn($x) => $x->whereDate('k.tanggal', '<=', $cutoff));
      };

      $query = $this->latestPesertaTable($filter);

      if ($status == 'akan') {
        $query->where('lp.sudah_diperiksa', false);
      } elseif ($status == 'sudah') {
        $query->where('lp.sudah_diperiksa', true);
      }

      $data = $query->select([
        'lp.siswa_id',
        'lp.nama',
        'lp.jenis_kelamin',
        'lp.usia',
        'lp.npsn',
        'lp.nama_sekolah',
        'lp.jenjang',
        'lp.kelurahan_dispenduk',
        'lp.tanggal',
        'lp.nama_puskesmas',
        'lp.nama_posyandu',
        'lp.sudah_diperiksa',
      ])
        ->orderBy('lp.nama_sekolah')
        ->orderBy('lp.nama')
        ->get();
    }

    // Rangkum daftar sekolah yang muncul di daftar murid ini, buat tabel "Sekolah"
    // di modal (collapsible).
    $sekolah = $data->groupBy('npsn')->map(function ($group) {
      $first = $group->first();
      return (object) [
        'npsn' => $first->npsn,
        'nama_sekolah' => $first->nama_sekolah,
        'jenjang_label' => self::LABEL_JENJANG[$first->jenjang] ?? $first->jenjang,
        'kelurahan_dispenduk' => $first->kelurahan_dispenduk,
        'nama_puskesmas' => $first->nama_puskesmas,
        'nama_posyandu' => $first->nama_posyandu,
        'jumlah_murid' => $group->count(),
        'akan_diperiksa' => $group->where('sudah_diperiksa', false)->count(),
        'sudah_diperiksa' => $group->where('sudah_diperiksa', true)->count(),
      ];
    })->values();

    $judul = 'Daftar Murid';
    if ($jenjang) {
      $judul .= ' ' . (self::LABEL_JENJANG[$jenjang] ?? $jenjang);
    }
    if ($status) {
      $judul .= ' - ' . self::LABEL_STATUS[$status];
    }
    if ($tanggal) {
      $judul .= ' - Tanggal ' . Carbon::parse($tanggal)->format('d-m-Y');
    } elseif ($bulan && $bulan != 'semua' && $tahun) {
      $judul .= ' - ' . self::NAMA_BULAN[$bulan] . ' ' . $tahun;
    } elseif ($tahun && $tahun != 'semua') {
      $judul .= ' - Tahun ' . $tahun;
    }
    if ($cutoff) {
      $judul .= ' (s.d. ' . Carbon::parse($cutoff)->format('d-m-Y') . ')';
    }

    $content = view('dinas.rekap_puskesmas.modal_nama', [
      'judul' => $judul,
      'data' => $data,
      'sekolah' => $sekolah,
    ])->render();

    return ['content' => $content];
  }
}
