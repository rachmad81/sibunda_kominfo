<?php

namespace App\Http\Controllers\Bunda;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DB, Auth;

class DiagramAnakController extends Controller
{
  function main()
  {
    $kecamatan = DB::table('public.kecamatan')->orderBy('kecamatan_dispenduk')->get();
    $data = [
      'kecamatan' => $kecamatan,
    ];
    return view('bunda.diagram_anak.index', $data);
  }

  function get_kelurahan(Request $request)
  {
    $kode = $request->kode;
    $dukcapil = $request->dukcapil;

    if ($kode == 'All') {
      return ['code' => 250];
    } else {
      if (Auth::getUser()->tingkat == 'kelurahan') {
        $kelurahan = DB::table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->orderBy('kelurahan_dispenduk')->get();
      } else {
        $kelurahan = DB::table('public.kelurahan')->where('kecamatan_kode', $kode)->orderBy('kelurahan_dispenduk')->get();
      }

      return ['code' => 200, 'kelurahan' => $kelurahan];
    }
  }

  function keyword()
  {
    $keywords = [
      'pindah',
      'meninggal',
      'kesehatan',
      'kosong',
      'cacat',
      'tahui',
      'posisi',
      'abk',
      'urus',
      'Faktor Ekonomi',
      'Faktor Aksesibilitas / Jarak',
      'Faktor Sosial Budaya',
      'Faktor kondisi khusus anak (kesehatan/disabilitas/inklusi)',
      'Alasan Administratif',
    ];

    return $keywords;
  }

  function get_data(Request $request)
  {
    $kecamatan = $request->kecamatan; //"All"
    $kelurahan = $request->kelurahan; //"All"
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $judul = 'Data Diagram Anak Usia 5-6 Tahun';
    if ($kecamatan == 'All') {
      $judul .= ' se-Kota Surabaya';
    } else {
      if ($kelurahan == 'All') {
        $judul .= ' se-Kecamatan ' . $kecamatan;
      } else {
        if (Auth::getUser()->tingkat == 'kelurahan') {
          $data_kecamatan = DB::table('public.kelurahan as kel')->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
          $kecamatan = $data_kecamatan->kecamatan_dispenduk;
          $kecamatan_kode = $data_kecamatan->kecamatan_kode;
          $judul .= ' se-Kelurahan ' . $kelurahan . ', Kecamatan ' . $kecamatan;
        } else {
          $judul .= ' se-Kelurahan ' . $kelurahan . ', Kecamatan ' . $kecamatan;
        }
      }
    }

    $data = [
      'judul' => $judul,
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan_kode' => $kelurahan_kode,
    ];

    $content = view('bunda.diagram_anak.tabelnya', $data)->render();
    return ['code' => 200, 'content' => $content];
  }

  function get_jumlah(Request $request)
  {
    $i = $request->i;
    $ket = $request->keterangan;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;
    $reload = $request->reload;

    $tahun_awal = date('Y') - 6;
    $tahun_akhir = date('Y') - 5;

    $where0 = "id is not null";
    $where2 = "s.npsn is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
      $where2 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
        $where2 .= " AND s.kec_id='$kecamatan_kode'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
        $where2 .= " AND s.desa='$kelurahan_kode'";
      }
    }

    $jumlah_lembaga = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
      ->whereRaw("$where2 AND s.nama not ilike ALL(ARRAY['%terbuka%','%selalu%','WUSTHA%','ula%','pps%','pdf%','WUSTHO%']) AND u.user_enable is true")
      ->whereRaw("u.user_enable is true")
      ->whereIn('s.jenjang', ['TPA', 'SPS', 'RA', 'KB', 'TK'])
      ->get();

    $keywords = $this->keyword();

    switch ($i) {
      case '0':
        // 'Jumlah Anak usia 5-6 tahun',
        $jumlah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
          ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis,tandai_tidak_sekolah,CASE WHEN detail_tidak_sekolah is null OR detail_tidak_sekolah='' THEN '-' ELSE detail_tidak_sekolah END as detail_tidak_sekolah")
          ->whereRaw("$where0")->get();
        $jumlah_total = collect($jumlah)->count();
        $jumlah_profil_ringkas = collect($jumlah)->filter(function ($item) {
          return ($item->status === 'Ada' || $item->status_sekolah === 'Sudah' || $item->status_sekolah === 'Akan sekolah') && $item->tandai_tidak_sekolah !== 'ya';
        })->count();
        $jumlah_profil = collect($jumlah)->filter(function ($item) {
          return $item->status === 'Ada';
        })->count();
        $jumlah_tdk_profil = collect($jumlah)->filter(function ($item) {
          return $item->status !== 'Ada';
        })->count();
        // $jumlah_tdk_sesuai_kk = collect($jumlah)->filter(function($item){
        // 	// return ($item->status!=='Ada' || $item->status_sekolah!=='Sudah' || $item->status_sekolah!=='Akan sekolah');
        // 	return $item->status!=='Ada' && ($item->status_alamat==='Tidak sesuai' || $item->status_alamat===null);
        // })->count();

        $jumlah_akan_sekolah = collect($jumlah)->filter(function ($item) {
          return $item->status !== 'Ada' && $item->status_sekolah === 'Akan sekolah';
        })->count();

        $jumlah_sudah_sekolah = collect($jumlah)->filter(function ($item) {
          return $item->status !== 'Ada' && $item->status_sekolah === 'Sudah';
        })->count();

        $jumlah_tdk_sekolah = collect($jumlah)->filter(function ($item) {
          return $item->tandai_tidak_sekolah === 'ya';
        })->count();
        $jumlah_tdk_sekolah_tertangani = collect($jumlah)
          ->filter(function ($item) use ($keywords) {

            // Normalisasi nilai agar aman
            $tandai = $item->tandai_tidak_sekolah ?? '';
            $status = $item->status_sekolah ?? '';
            $detail = strtolower((string) $item->detail_tidak_sekolah);

            // Cek apakah detail mengandung salah satu keyword
            $matchKeyword = collect($keywords)->contains(function ($keyword) use ($detail) {
              return Str::contains($detail, strtolower($keyword));
            });

            // Gabungkan seluruh kondisi OR
            return (
              $tandai === 'ya' && (($matchKeyword && $status === 'Tidak sekolah') || $status !== 'Tidak sekolah')
            );
          })
          ->count();
        $jumlah_tdk_sekolah_gamis = collect($jumlah)->filter(function ($item) {
          return $item->tandai_tidak_sekolah === 'ya' && $item->status_gamis === 'GAMIS/PRAMIS';
        })->count();
        $jumlah_tdk_sekolah_gamis_tertangani = collect($jumlah)
          ->filter(function ($item) use ($keywords) {

            // Normalisasi nilai agar aman
            $tandai = $item->tandai_tidak_sekolah ?? '';
            $status = $item->status_sekolah ?? '';
            $status_gamis = $item->status_gamis ?? '';
            $detail = strtolower((string) $item->detail_tidak_sekolah);

            // Cek apakah detail mengandung salah satu keyword
            $matchKeyword = collect($keywords)->contains(function ($keyword) use ($detail) {
              return Str::contains($detail, strtolower($keyword));
            });

            // Gabungkan seluruh kondisi OR
            return (
              ($tandai === 'ya' && $status_gamis == 'GAMIS/PRAMIS') && (($matchKeyword && $status === 'Tidak sekolah') || $status !== 'Tidak sekolah')
            );
          })->count();

        $id_tidak_sekolah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->select('id')
          ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
            return $q->where('kecamatan', $kecamatan);
          })
          ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
            return $q->where('kelurahan', $kelurahan);
          })
          ->where('tandai_tidak_sekolah', 'ya');
        $id_semua = DB::connection('pgsql')->table('history.anak_belum_sekolah')->select('id')
          ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
            return $q->where('kecamatan', $kecamatan);
          })
          ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
            return $q->where('kelurahan', $kelurahan);
          })
          ->whereRaw("(status='Ada' OR status_sekolah='Sudah' OR status_sekolah='Akan sekolah')")
          ->unionAll($id_tidak_sekolah);
        $jumlah_tdk_sesuai_kk = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
          ->when($kecamatan != 'All', function ($q) use ($kecamatan) {
            return $q->where('kecamatan', $kecamatan);
          })
          ->when($kelurahan != 'All', function ($q) use ($kelurahan) {
            return $q->where('kelurahan', $kelurahan);
          })
          ->whereNotIn('id', $id_semua)
          ->count();

        $id = 'penduduk';

        $dril = [
          [
            'name' => 'Jumlah Total',
            'y' => $jumlah_total,
            'kategori' => $id,
          ],
          [
            'name' => 'Anak sekolah hasil survey bunda PAUD',
            'y' => $jumlah_profil_ringkas,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah tidak sesuai KK/ pindah',
            'y' => $jumlah_tdk_sesuai_kk,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah tidak sekolah',
            'y' => $jumlah_tdk_sekolah,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah survey ulang',
            'y' => $jumlah_tdk_sekolah_tertangani,
            'kategori' => $id,
            'color' => '#f2ad4b',
          ],
          [
            'name' => 'Jumlah tidak sekolah gamis/pramis',
            'y' => $jumlah_tdk_sekolah_gamis,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah survey ulang gamis/pramis',
            'y' => $jumlah_tdk_sekolah_gamis_tertangani,
            'kategori' => $id,
            'color' => '#f2ad4b',
          ],

          // ['Jumlah ada di profil', $jumlah_profil],
          // ['Jumlah tidak ada di profil', $jumlah_tdk_profil],
          // ['Jumlah tidak ada di profil akan sekolah', $jumlah_akan_sekolah],
          // ['Jumlah tidak ada di profil sudah sekolah', $jumlah_sudah_sekolah],
          // ['Jumlah tidak ada di profil tidak sekolah', $jumlah_tdk_sekolah],
          // ['Jumlah tidak ada di profil tidak sesuai KK', $jumlah_tdk_sesuai_kk],
          // ['Jumlah tidak ada di profil gamis/pramis', $jumlah_tdk_sekolah_gamis],
        ];

        return ['i' => $i, 'jumlah_display' => number_format($jumlah_total, 0, '', '.'), 'jumlah' => $jumlah_total, 'dril' => $dril, 'id_kolom' => 'penduduk'];
        break;

      case '1':
        // 'Jumlah Siswa',
        if (Cache::has('diagram_anak') && $kecamatan == 'All' && $reload == null) {
          $dril = json_decode(Cache::get('diagram_anak'));
          $jumlah_total = $dril[0]->y;
        } else {
          $tahun_awal = date('Y') - 6;
          $tahun_akhir = date('Y') - 5;
          $awal = Carbon::parse($tahun_awal . '-01-01');
          $akhir = Carbon::parse($tahun_akhir . '-12-31');

          $npsn = collect($jumlah_lembaga)->pluck('npsn')->toArray();

          $jumlah = DB::connection('pgsql_paud')->table('public.siswa as s')
            ->join('public.wali_murid as wm', function ($join) {
              return $join->on('wm.id_siswa', '=', 's.id_siswa')->on('wm.npsn', '=', 's.npsn');
            })
            ->join('public.sekolah as se', 'se.npsn', 's.npsn')
            ->selectRaw("s.nama,s.nik,s.tempat_lahir,s.tgl_lahir,wm.alamat_rumah,wm.rt,wm.rw,wm.kec_id,wm.kel_id,wm.kab_id,s.tgl_lahir,
						CASE WHEN s.verified_gakin is true THEN 'PRAMIS' ELSE '' END as status_pramis,
						CASE WHEN s.verified_gakin2 is true THEN 'GAKIN' ELSE '' END as status_gakin,
						kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,se.npsn,se.nama as nama_sekolah
						")
            ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
            ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
            ->whereRaw("s.status_siswa='Aktif' AND s.alumni is not true")
            ->whereIn('s.npsn', $npsn)
            ->whereBetween('tgl_lahir', [$awal, $akhir])
            ->get();

          $jumlah_ats = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis,tandai_tidak_sekolah,pilihan_detail_tidak_sekolah,CASE WHEN detail_tidak_sekolah is null OR detail_tidak_sekolah='' THEN '-' ELSE detail_tidak_sekolah END as detail_tidak_sekolah")
            ->whereRaw("$where0")->get();

          $jumlah_usia57 = collect($jumlah)->count();
          $jumlah_usia57_sby = collect($jumlah)
            ->filter(function ($user) {
              return str_contains(strtolower($user->kab_id ?? ''), 'surabaya');
            })->count();
          // $jumlah_total = collect($jumlah)->count();
          $jumlah_usia57_nosby = collect($jumlah)
            ->filter(function ($user) {
              return !str_contains(strtolower($user->kab_id), strtolower('surabaya'));
            })->count();
          $id = 'siswa';

          $jumlah_total_ats = collect($jumlah_ats)->count();
          $jumlah_profil_ringkas = collect($jumlah_ats)->filter(function ($item) {
            return $item->status === 'Ada' || $item->status_sekolah === 'Sudah' || $item->status_sekolah === 'Akan sekolah';
          })->count();

          $jumlah_tdk_sekolah = collect($jumlah_ats)->filter(function ($item) {
            return $item->tandai_tidak_sekolah === 'ya';
          })->count();
          $jumlah_tdk_sekolah_tertangani = collect($jumlah_ats)
            ->filter(function ($item) use ($keywords) {

              // Normalisasi nilai agar aman
              $tandai = $item->tandai_tidak_sekolah ?? '';
              $status = $item->status_sekolah ?? '';
              $status_gamis = $item->status_gamis ?? '';
              $detail = strtolower((string) $item->detail_tidak_sekolah);

              // Cek apakah detail mengandung salah satu keyword
              $matchKeyword = collect($keywords)->contains(function ($keyword) use ($detail) {
                return Str::contains($detail, strtolower($keyword));
              });

              // Gabungkan seluruh kondisi OR
              return (
                $tandai === 'ya' && (($matchKeyword && $status === 'Tidak sekolah') || $status !== 'Tidak sekolah')
              );
            })->count();

          $jumlah_tdk_sesuai_kk = $jumlah_total_ats - $jumlah_profil_ringkas - $jumlah_tdk_sekolah;

          $nik_ringkas = collect($jumlah_ats)->filter(function ($item) {
            return $item->status === 'Ada' || $item->status_sekolah === 'Sudah' || $item->status_sekolah === 'Akan sekolah';
          })->pluck('nik')->toArray();
          // $jumlah_sebelum_survey = collect($jumlah)->whereNotIn('nik',$nik_ringkas)
          // ->filter(function ($user) {
          // 	return str_contains(strtolower($user->kab_id ?? ''), 'surabaya');
          // })->count();

          $jumlah_sebelum_survey = $jumlah_usia57_sby - $jumlah_profil_ringkas;

          $jumlah_total = $jumlah_usia57 + $jumlah_tdk_sekolah + $jumlah_tdk_sesuai_kk;

          $dril = [

            // [
            // 	'name'=>'Jumlah Total',
            // 	'y'=>$jumlah_total,
            // 	'kategori'=>$id,
            // ],
            // [
            // 	'name'=>'Jumlah Total KK Surabaya',
            // 	'y'=>$jumlah_total_sby,
            // 	'kategori'=>$id,
            // ],
            // [
            // 	'name'=>'Jumlah Total KK Bukan Surabaya',
            // 	'y'=>$jumlah_total_nosby,
            // 	'kategori'=>$id,
            // ],
            // [
            // 	'name'=>'Jumlah Pramis',
            // 	'y'=>$jumlah_total_pramis,
            // 	'kategori'=>$id,
            // ],
            // [
            // 	'name'=>'Jumlah Gamis',
            // 	'y'=>$jumlah_total_gakin,
            // 	'kategori'=>$id,
            // ],

            [
              'name' => 'Jumlah Penduduk usia 5-6',
              'y' => $jumlah_total,
              'kategori' => $id,
            ],
            [
              'name' => 'Jumlah siswa usia 5-6',
              'y' => $jumlah_usia57,
              'kategori' => $id,
            ],
            [
              'name' => 'Jumlah siswa setelah survey usia 5-6',
              'y' => $jumlah_usia57_sby,
              'kategori' => $id,
            ],
            [
              'name' => 'Jumlah siswa usia 5-6 sebelum survey (KK Surabaya)',
              'y' => $jumlah_sebelum_survey,
              'kategori' => $id,
            ],
            [
              'name' => 'Jumlah siswa usia 5-6 sebelum survey (KK Bukan Surabaya)',
              'y' => $jumlah_usia57_nosby,
              'kategori' => $id,
            ],
            [
              'name' => 'Jumlah ATS usia 5-6',
              'y' => $jumlah_total_ats,
              'kategori' => $id,
              'color' => '#544fc5',
            ],
            [
              'name' => 'Jumlah hasil survey bunda PAUD',
              'y' => $jumlah_profil_ringkas,
              'kategori' => $id,
              'color' => '#544fc5',
            ],
            [
              'name' => 'Jumlah tidak sesuai KK/ pindah',
              'y' => $jumlah_tdk_sesuai_kk,
              'kategori' => $id,
              'color' => '#544fc5',
            ],
            [
              'name' => 'Jumlah tidak sekolah',
              'y' => $jumlah_tdk_sekolah,
              'kategori' => $id,
              'color' => '#544fc5',
            ],
            [
              'name' => 'Jumlah survey ulang',
              'y' => $jumlah_tdk_sekolah_tertangani,
              'kategori' => $id,
              'color' => '#f2ad4b',
            ],
          ];

          if ($kecamatan == 'All') {
            Cache::put('diagram_anak', json_encode($dril), 1800);
          }
        }

        return ['i' => $i, 'jumlah_display' => number_format($jumlah_total, 0, '', '.'), 'jumlah' => $jumlah_total, 'dril' => $dril, 'id_kolom' => 'siswa'];
        break;

      case '2':
        // 'Jumlah Lembaga',
        $jumlah = $jumlah_lembaga;

        $jumlah_total = collect($jumlah)->count();
        $jumlah_tpa = collect($jumlah)->where('jenjang', 'TPA')->count();
        $jumlah_sps = collect($jumlah)->where('jenjang', 'SPS')->count();
        $jumlah_kb = collect($jumlah)->where('jenjang', 'KB')->count();
        $jumlah_tk = collect($jumlah)->where('jenjang', 'TK')->count();
        $jumlah_ra = collect($jumlah)->where('jenjang', 'RA')->count();
        $id = 'lembaga';

        $dril = [
          [
            'name' => 'Jumlah Lembaga Total',
            'y' => $jumlah_total,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Lembaga TPA',
            'y' => $jumlah_tpa,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Lembaga SPS/PPT',
            'y' => $jumlah_sps,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Lembaga KB',
            'y' => $jumlah_kb,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Lembaga TK',
            'y' => $jumlah_tk,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Lembaga RA',
            'y' => $jumlah_ra,
            'kategori' => $id,
          ],
        ];

        return ['i' => $i, 'jumlah_display' => number_format($jumlah_total, 0, '', '.'), 'jumlah' => $jumlah_total, 'dril' => $dril, 'id_kolom' => 'lembaga'];
        break;

      case '3':
        // 'Jumlah GTK',
        $npsn = collect($jumlah_lembaga)->pluck('npsn')->toArray();
        $jumlah = DB::connection('pgsql_paud')->table('public.pegawai as s')
          ->selectRaw("s.nama,s.npsn,s.induk,s.jabatan,s.pns")
          ->whereRaw("s.keterangan='Aktif'")
          ->whereIn('s.npsn', $npsn)
          ->get();

        $jumlah_total = collect($jumlah)->count();
        $jumlah_induk = collect($jumlah)->where('induk', 'true')->count();
        $jumlah_non_induk = collect($jumlah)->where('induk', '!=', 'true')->count();
        $jumlah_ks = collect($jumlah)->where('jabatan', '2')->count();
        $jumlah_guru = collect($jumlah)->whereIn('jabatan', ['1', '3', '4', '5', '7', '8', '9', '11', '12', '13', '19', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '46', '47', '48', '49', '50', '51', '52', '53'])->count();
        $jumlah_tendik = collect($jumlah)->whereNotIn('jabatan', ['1', '2', '3', '4', '5', '7', '8', '9', '11', '12', '13', '19', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '46', '47', '48', '49', '50', '51', '52', '53'])->count();
        $jumlah_pns = collect($jumlah)->where('pns', '1')->count();
        $id = 'pegawai';

        $dril = [
          [
            'name' => 'Jumlah Pegawai Total',
            'y' => $jumlah_total,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Kepala Sekolah',
            'y' => $jumlah_ks,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Guru',
            'y' => $jumlah_guru,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Tendik',
            'y' => $jumlah_tendik,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Pegawai Induk',
            'y' => $jumlah_induk,
            'kategori' => $id,
          ],
          [
            'name' => 'Jumlah Pegawai PNS',
            'y' => $jumlah_pns,
            'kategori' => $id,
          ],
        ];

        return ['i' => $i, 'jumlah_display' => number_format($jumlah_total, 0, '', '.'), 'jumlah' => $jumlah_total, 'dril' => $dril, 'id_kolom' => 'pegawai'];
        break;

      default:
        $jumlah = 0;
        break;
    }

    return ['i' => $i, 'jumlah' => number_format($jumlah, 0, '', '.'), 'jumlah_murni' => $jumlah];
  }

  function form_detail(Request $request)
  {
    $jenis = $request->jenis;
    $kategori = $request->kategori;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;
    $tabel_detail = '';

    $keywords = $this->keyword();;
    $str_keyword = "'%";
    $str_keyword .= implode("%','%", $keywords);
    $str_keyword .= "%'";

    $where0 = "id is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
      }
    }

    $kategori_tidak_sekolah = [
      'Faktor Ekonomi',
      'Faktor Aksesibilitas / Jarak',
      'Faktor Sosial Budaya',
      'Faktor kondisi khusus anak (kesehatan/disabilitas/inklusi)',
      'Alasan Administratif',
    ];
    $str_kategori = "'%";
    $str_kategori .= implode("%','%", $kategori_tidak_sekolah);
    $str_kategori .= "%'";

    if (in_array($jenis, [
      'Jumlah survey ulang',
      'Jumlah survey ulang gamis/pramis'
    ])) {
      $tabel_detail0 = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->selectRaw("
				status_sekolah as detail_kategori,COUNT('status_sekolah') as jumlah
				")->whereRaw("
					$where0 AND tandai_tidak_sekolah='ya' AND status_sekolah!='Tidak sekolah'
				")
        ->when($jenis == 'Jumlah survey ulang gamis/pramis', function ($item) {
          return $item->where('status_gamis', 'GAMIS/PRAMIS');
        })
        ->groupByRaw("status_sekolah")->get()->toArray();
      $tabel_detail1 = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')->selectRaw("
				CASE WHEN detail_tidak_sekolah ilike any(array[" . $str_kategori . "]) THEN detail_tidak_sekolah ELSE
				CASE WHEN detail_tidak_sekolah ilike '%pindah%' THEN 'Pindah' ELSE
				CASE WHEN detail_tidak_sekolah ilike '%meninggal%' THEN 'Meninggal' ELSE 'Lainnya'
				END END END as detail_kategori,
				COUNT(*) as jumlah")->whereRaw("
					$where0 AND tandai_tidak_sekolah='ya' AND (detail_tidak_sekolah ilike any(array[" . $str_keyword . "]) AND status_sekolah='Tidak sekolah')
				")
        ->when($jenis == 'Jumlah survey ulang gamis/pramis', function ($item) {
          return $item->where('status_gamis', 'GAMIS/PRAMIS');
        })
        ->groupByRaw("detail_kategori")
        ->get()->toArray();
      $tabel_detail = array_merge($tabel_detail0, $tabel_detail1);
      $urutan = collect($tabel_detail)->pluck('detail_kategori')->toArray();
      $key = array_search('Lainnya', $urutan);
      unset($urutan[$key]);
      $urutan = array_values($urutan);

      $tabel_detail = collect($tabel_detail)->sortBy(function ($item) use ($urutan) {
        // Jika kategorinya "Lainnya" → taruh paling belakang
        if ($item->detail_kategori === 'Lainnya') {
          return PHP_INT_MAX;
        }

        // Jika tidak ditemukan di array urutan → letakkan sebelum "Lainnya"
        $pos = array_search($item->detail_kategori, $urutan);

        return $pos === false ? count($urutan) : $pos;
      })->values();
    }

    switch ($kategori) {
      case 'penduduk':
        if (in_array($jenis, [
          'Jumlah survey ulang',
          'Jumlah survey ulang gamis/pramis',
          // 'Jumlah tidak sekolah'
        ])) {
          $kolom = [
            'No',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'catatan'
          ];

          $kolom_body = [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama', 'name' => 'nama', 'title' => 'Nama'],
            ['data' => 'nik', 'name' => 'nik', 'title' => 'NIK'],
            ['data' => 'tempat_lahir', 'name' => 'tempat_lahir', 'title' => 'Tempat Lahir'],
            ['data' => 'tgl_lahir', 'name' => 'tgl_lahir', 'title' => 'Tanggal Lahir'],
            ['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat'],
            ['data' => 'rt', 'name' => 'rt', 'title' => 'RT'],
            ['data' => 'rw', 'name' => 'rw', 'title' => 'RW'],
            ['data' => 'kelurahan', 'name' => 'kelurahan', 'title' => 'Kelurahan'],
            ['data' => 'kecamatan', 'name' => 'kecamatan', 'title' => 'Kecamatan'],
            ['data' => 'catatan', 'name' => 'catatan', 'title' => 'Catatan']
          ];
        } else {
          $kolom = [
            'No',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan'
          ];

          $kolom_body = [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama', 'name' => 'nama', 'title' => 'Nama'],
            ['data' => 'nik', 'name' => 'nik', 'title' => 'NIK'],
            ['data' => 'tempat_lahir', 'name' => 'tempat_lahir', 'title' => 'Tempat Lahir'],
            ['data' => 'tgl_lahir', 'name' => 'tgl_lahir', 'title' => 'Tanggal Lahir'],
            ['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat'],
            ['data' => 'rt', 'name' => 'rt', 'title' => 'RT'],
            ['data' => 'rw', 'name' => 'rw', 'title' => 'RW'],
            ['data' => 'kelurahan', 'name' => 'kelurahan', 'title' => 'Kelurahan'],
            ['data' => 'kecamatan', 'name' => 'kecamatan', 'title' => 'Kecamatan']
          ];
        }

        $judul = 'Data terindikasi ATS usia 5-6 tahun';
        $judul .= ' (' . $jenis . ')';
        break;

      case 'siswa':
        if (in_array($jenis, [
          'Jumlah ATS usia 5-6',
          'Jumlah hasil survey bunda PAUD',
          'Jumlah tidak sesuai KK/ pindah',
          'Jumlah tidak sekolah',
          'Jumlah Penduduk usia 5-6',
          'Jumlah siswa usia 5-6 sebelum survey (KK Surabaya)',
        ])) {
          $kolom = [
            'No',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan'
          ];

          $kolom_body = [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama', 'name' => 'nama', 'title' => 'Nama'],
            ['data' => 'nik', 'name' => 'nik', 'title' => 'NIK'],
            ['data' => 'tempat_lahir', 'name' => 'tempat_lahir', 'title' => 'Tempat Lahir'],
            ['data' => 'tgl_lahir', 'name' => 'tgl_lahir', 'title' => 'Tanggal Lahir'],
            ['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat'],
            ['data' => 'rt', 'name' => 'rt', 'title' => 'RT'],
            ['data' => 'rw', 'name' => 'rw', 'title' => 'RW'],
            ['data' => 'kelurahan', 'name' => 'kelurahan', 'title' => 'Kelurahan'],
            ['data' => 'kecamatan', 'name' => 'kecamatan', 'title' => 'Kecamatan']
          ];

          $judul = 'Data terindikasi ATS usia 5-6 tahun';
          $judul .= ' (' . $jenis . ')';
        } else if (in_array($jenis, [
          'Jumlah survey ulang'
        ])) {
          $kolom = [
            'No',
            'Nama',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'catatan'
          ];

          $kolom_body = [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama', 'name' => 'nama', 'title' => 'Nama'],
            ['data' => 'nik', 'name' => 'nik', 'title' => 'NIK'],
            ['data' => 'tempat_lahir', 'name' => 'tempat_lahir', 'title' => 'Tempat Lahir'],
            ['data' => 'tgl_lahir', 'name' => 'tgl_lahir', 'title' => 'Tanggal Lahir'],
            ['data' => 'alamat', 'name' => 'alamat', 'title' => 'Alamat'],
            ['data' => 'rt', 'name' => 'rt', 'title' => 'RT'],
            ['data' => 'rw', 'name' => 'rw', 'title' => 'RW'],
            ['data' => 'kelurahan', 'name' => 'kelurahan', 'title' => 'Kelurahan'],
            ['data' => 'kecamatan', 'name' => 'kecamatan', 'title' => 'Kecamatan'],
            ['data' => 'catatan', 'name' => 'catatan', 'title' => 'Catatan']
          ];

          $judul = 'Data terindikasi ATS usia 5-6 tahun';
          $judul .= ' (' . $jenis . ')';
        } else {
          $kolom = [
            'No',
            'Nama',
            'NIK',
            'Nama Sekolah',
            'NPSN',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kota/Kabupaten'
          ];

          $kolom_body = [
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
            ['data' => 'nama', 'name' => 's.nama', 'title' => 'Nama'],
            ['data' => 'nik', 'name' => 's.nik', 'title' => 'NIK'],
            ['data' => 'nama_sekolah', 'name' => 'se.nama', 'title' => 'Nama Sekolah'],
            ['data' => 'npsn', 'name' => 'se.npsn', 'title' => 'NPSN'],
            ['data' => 'tempat_lahir', 'name' => 's.tempat_lahir', 'title' => 'Tempat Lahir'],
            ['data' => 'tgl_lahir', 'name' => 's.tgl_lahir', 'title' => 'Tanggal Lahir'],
            ['data' => 'alamat_rumah', 'name' => 'wm.alamat_rumah', 'title' => 'Alamat'],
            ['data' => 'rt', 'name' => 'wm.rt', 'title' => 'RT'],
            ['data' => 'rw', 'name' => 'wm.rw', 'title' => 'RW'],
            ['data' => 'kelurahan_dispenduk', 'name' => 'kel.kelurahan_dispenduk', 'title' => 'Kelurahan'],
            ['data' => 'kecamatan_dispenduk', 'name' => 'kec.kecamatan_dispenduk', 'title' => 'Kecamatan'],
            ['data' => 'kab_id', 'name' => 'wm.kab_id', 'title' => 'Kota/Kabupaten']
          ];

          $judul = 'Data Jumlah Penduduk';
          $judul .= ' (' . $jenis . ')';
        }

        break;

      case 'lembaga':
        $kolom = [
          'No',
          'Nama Sekolah',
          'NPSN',
          'Jenjang',
          'Status',
          'Alamat',
          'RT',
          'RW',
          'Kelurahan',
          'Kecamatan'
        ];

        $kolom_body = [
          ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
          ['data' => 'nama', 'name' => 's.nama', 'title' => 'Nama Sekolah'],
          ['data' => 'npsn', 'name' => 's.npsn', 'title' => 'NPSN'],
          ['data' => 'jenjang', 'name' => 's.jenjang', 'title' => 'NPSN'],
          ['data' => 'status', 'name' => 's.status', 'title' => 'NPSN'],
          ['data' => 'alamat', 'name' => 's.alamat', 'title' => 'Alamat'],
          ['data' => 'rt', 'name' => 's.rt', 'title' => 'RT'],
          ['data' => 'rw', 'name' => 's.rw', 'title' => 'RW'],
          ['data' => 'kelurahan_dispenduk', 'name' => 'kel.kelurahan_dispenduk', 'title' => 'Kelurahan'],
          ['data' => 'kecamatan_dispenduk', 'name' => 'kec.kecamatan_dispenduk', 'title' => 'Kecamatan']
        ];

        $judul = 'Data Jumlah Lembaga';
        $judul .= ' (' . $jenis . ')';
        break;

      case 'pegawai':
        $kolom = [
          'No',
          'Nama',
          'NIK',
          'Jabatan',
          'Nama Sekolah',
          'NPSN',
          'Alamat',
          'RT',
          'RW',
          'Kelurahan',
          'Kecamatan',
          'Kota/Kabupaten'
        ];

        $kolom_body = [
          ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false],
          ['data' => 'nama', 'name' => 'p.nama', 'title' => 'Nama'],
          ['data' => 'no_ktp', 'name' => 'p.no_ktp', 'title' => 'NIK'],
          ['data' => 'nama_jabatan', 'name' => 'j.nama', 'title' => 'Nama Jabatan'],
          ['data' => 'nama_sekolah', 'name' => 'se.nama', 'title' => 'Nama Sekolah'],
          ['data' => 'npsn', 'name' => 'se.npsn', 'title' => 'NPSN'],
          ['data' => 'alamat', 'name' => 'p.alamat', 'title' => 'Alamat'],
          ['data' => 'rt', 'name' => 'p.rt', 'title' => 'RT'],
          ['data' => 'rw', 'name' => 'p.rw', 'title' => 'RW'],
          ['data' => 'kelurahan_dispenduk', 'name' => 'kel.kelurahan_dispenduk', 'title' => 'Kelurahan'],
          ['data' => 'kecamatan_dispenduk', 'name' => 'kec.kecamatan_dispenduk', 'title' => 'Kecamatan'],
          ['data' => 'kab_id', 'name' => 'p.kab_id', 'title' => 'Kota/Kabupaten']
        ];

        $judul = 'Data Jumlah Pegawai';
        $judul .= ' (' . $jenis . ')';
        break;

      default:
        $jumlah = [];
        break;
    }

    $data = [
      'kolom' => $kolom,
      'kolom_body' => json_encode($kolom_body),
      'judul' => $judul,
      'jenis' => $jenis,
      'kategori' => $kategori,
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kecamatan_kode' => $kecamatan_kode,
      'kelurahan_kode' => $kelurahan_kode,
      'tabel_detail' => $tabel_detail,
    ];

    $content = view('bunda.diagram_anak.detail', $data)->render();
    return ['content' => $content];
  }

  function get_detail(Request $request)
  {
    $jenis = $request->jenis;
    $kategori = $request->kategori;
    $kecamatan = $request->kecamatan;
    $kelurahan = $request->kelurahan;
    $kecamatan_kode = $request->kecamatan_kode;
    $kelurahan_kode = $request->kelurahan_kode;

    $tahun_awal = date('Y') - 6;
    $tahun_akhir = date('Y') - 5;

    $where0 = "id is not null";
    $where2 = "s.npsn is not null";
    if ($kecamatan == 'All') {
      $where0 .= "";
      $where2 .= "";
    } else {
      if ($kelurahan == 'All') {
        $where0 .= " AND kecamatan='$kecamatan'";
        $where2 .= " AND s.kec_id='$kecamatan_kode'";
      } else {
        $where0 .= " AND kelurahan='$kelurahan'";
        $where2 .= " AND s.desa='$kelurahan_kode'";
      }
    }

    $jumlah_lembaga = DB::connection('pgsql_paud')->table('public.sekolah as s')
      ->join('public.master_user as u', 'u.username', 's.npsn')
      ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->selectRaw("s.nama,s.npsn,s.jenjang")
      ->whereRaw("$where2 AND s.nama not ilike ALL(ARRAY['%terbuka%','%selalu%','WUSTHA%','ula%','pps%','pdf%','WUSTHO%']) AND u.user_enable is true")
      ->whereRaw("u.user_enable is true")
      ->whereIn('s.jenjang', ['TPA', 'SPS', 'RA', 'KB', 'TK'])
      ->get();

    $keywords = $this->keyword();;
    $str_keyword = "'%";
    $str_keyword .= implode("%','%", $keywords);
    $str_keyword .= "%'";

    switch ($kategori) {
      case 'penduduk':
        // 'Jumlah Anak usia 5-6 tahun',
        if ($jenis == 'Jumlah tidak sesuai KK/ pindah') {
          $tidak_dipakai = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("((status='Ada' OR status_sekolah='Sudah' OR status_sekolah='Akan sekolah'))")
            ->whereRaw("$where0")->get()->pluck('id')->toArray();
          $tidak_dipakai1 = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("tandai_tidak_sekolah='ya'")
            ->whereRaw("$where0")->get()->pluck('id')->toArray();
          $id = array_merge($tidak_dipakai, $tidak_dipakai1);

          $jumlah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis,pilihan_detail_tidak_sekolah,CASE WHEN detail_tidak_sekolah is null OR detail_tidak_sekolah='' THEN '-' ELSE detail_tidak_sekolah END as detail_tidak_sekolah,detail_sekolah")
            ->whereRaw("$where0")->whereNotIn('id', $id);
        } else {
          $jumlah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,tandai_tidak_sekolah,pilihan_detail_tidak_sekolah,CASE WHEN detail_tidak_sekolah is null OR detail_tidak_sekolah='' THEN '-' ELSE detail_tidak_sekolah END as detail_tidak_sekolah,detail_sekolah")
            ->when($jenis == 'Anak sekolah hasil survey bunda PAUD', function ($query) {
              return $query->whereRaw("((status='Ada' OR status_sekolah='Sudah' OR status_sekolah='Akan sekolah') OR tandai_tidak_sekolah!='ya')");
            })
            ->when($jenis == 'Jumlah tidak sekolah', function ($query) {
              return $query->whereRaw("tandai_tidak_sekolah='ya'");
            })
            ->when($jenis == 'Jumlah survey ulang', function ($query) use ($str_keyword) {
              return $query->whereRaw("tandai_tidak_sekolah='ya' AND ((detail_tidak_sekolah ilike any(array[" . $str_keyword . "]) AND status_sekolah='Tidak sekolah') OR status_sekolah!='Tidak sekolah')");
            })
            ->when($jenis == 'Jumlah tidak sekolah gamis/pramis', function ($query) {
              return $query->whereRaw("status_gamis='GAMIS/PRAMIS' AND tandai_tidak_sekolah='ya'");
            })
            ->when($jenis == 'Jumlah survey ulang gamis/pramis', function ($query) use ($str_keyword) {
              return $query->whereRaw("(tandai_tidak_sekolah='ya' AND status_gamis='GAMIS/PRAMIS') AND ((detail_tidak_sekolah ilike any(array[" . $str_keyword . "]) AND status_sekolah='Tidak sekolah') OR status_sekolah!='Tidak sekolah')");
            })
            ->whereRaw("$where0");
        }

        return DataTables::of($jumlah)->addIndexColumn()
          ->addColumn('catatan', function ($item) {
            $catatan = '';
            if ($item->status_sekolah == 'Tidak sekolah') {
              if ($item->pilihan_detail_tidak_sekolah != '') {
                $catatan .= '(<b>' . $item->detail_tidak_sekolah . '</b>) ' . $item->pilihan_detail_tidak_sekolah;
              } else {
                if ($item->detail_tidak_sekolah != '-') {
                  $catatan .= '(<b>Lainnya</b>) ' . $item->detail_tidak_sekolah;
                }
              }
            } else {
              $catatan .= '(<b>' . $item->status_sekolah . '</b>) ';
              if ($item->detail_sekolah != '') {
                $catatan .= $item->detail_sekolah;
              }
            }

            return $catatan;
          })
          ->rawColumns(['catatan'])
          ->make(true);
        break;

      case 'siswa':
        // 'Jumlah Siswa',
        $tahun_awal = date('Y') - 6;
        $tahun_akhir = date('Y') - 5;
        $awal = Carbon::parse($tahun_awal . '-01-01');
        $akhir = Carbon::parse($tahun_akhir . '-12-31');

        $npsn = collect($jumlah_lembaga)->pluck('npsn')->toArray();

        if (in_array($jenis, [
          'Jumlah tidak sesuai KK/ pindah',
        ])) {
          $tidak_dipakai = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("(status='Ada' OR status_sekolah='Sudah' OR status_sekolah='Akan sekolah')")
            ->whereRaw("$where0")->pluck('id')->toArray();
          $tidak_dipakai1 = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("tandai_tidak_sekolah='ya'")
            ->whereRaw("$where0")->pluck('id')->toArray();
          $id = array_merge($tidak_dipakai, $tidak_dipakai1);

          $jumlah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis,pilihan_detail_tidak_sekolah,CASE WHEN detail_tidak_sekolah is null OR detail_tidak_sekolah='' THEN '-' ELSE detail_tidak_sekolah END as detail_tidak_sekolah,detail_sekolah")
            ->whereRaw("$where0")->whereNotIn('id', $id);
        } else if (in_array($jenis, [
          'Jumlah ATS usia 5-6',
          'Jumlah hasil survey bunda PAUD',
          'Jumlah tidak sekolah',
          'Jumlah survey ulang'
        ])) {
          $jumlah = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis,tandai_tidak_sekolah,pilihan_detail_tidak_sekolah,CASE WHEN detail_tidak_sekolah is null OR detail_tidak_sekolah='' THEN '-' ELSE detail_tidak_sekolah END as detail_tidak_sekolah,detail_sekolah")
            ->when($jenis == 'Jumlah hasil survey bunda PAUD', function ($query) {
              return $query->whereRaw("(status='Ada' OR status_sekolah='Sudah' OR status_sekolah='Akan sekolah')");
            })
            ->when($jenis == 'Jumlah tidak sesuai KK/ pindah', function ($query) {
              return $query->whereRaw("status!='Ada' AND status_alamat!='Sesuai'");
            })
            ->when($jenis == 'Jumlah tidak sekolah', function ($query) {
              return $query->whereRaw("tandai_tidak_sekolah='ya'");
            })
            ->when($jenis == 'Jumlah survey ulang', function ($query) use ($str_keyword) {
              return $query->whereRaw("tandai_tidak_sekolah='ya' AND ((detail_tidak_sekolah ilike any(array[" . $str_keyword . "]) AND status_sekolah='Tidak sekolah') OR status_sekolah!='Tidak sekolah')");
            })
            ->whereRaw("$where0");

          return DataTables::of($jumlah)->addIndexColumn()
            ->addColumn('catatan', function ($item) {
              $catatan = '';
              if ($item->status_sekolah == 'Tidak sekolah') {
                if ($item->pilihan_detail_tidak_sekolah != '') {
                  $catatan .= '(<b>' . $item->detail_tidak_sekolah . '</b>) ' . $item->pilihan_detail_tidak_sekolah;
                } else {
                  if ($item->detail_tidak_sekolah != '-') {
                    $catatan .= '(<b>Lainnya</b>) ' . $item->detail_tidak_sekolah;
                  }
                }
              } else {
                $catatan .= '(<b>' . $item->status_sekolah . '</b>) ';
                if ($item->detail_sekolah != '') {
                  $catatan .= $item->detail_sekolah;
                }
              }

              return $catatan;
            })
            ->rawColumns(['catatan'])
            ->make(true);
        } elseif (in_array($jenis, [
          'Jumlah siswa usia 5-6',
          'Jumlah siswa setelah survey usia 5-6',
          'Jumlah siswa usia 5-6 sebelum survey (KK Bukan Surabaya)'
        ])) {
          $dt_awal = DB::connection('pgsql_paud')->table('public.siswa as s')
            ->join('public.wali_murid as wm', function ($join) {
              return $join->on('wm.id_siswa', '=', 's.id_siswa')->on('wm.npsn', '=', 's.npsn');
            })
            ->join('public.sekolah as se', 'se.npsn', 's.npsn')
            ->selectRaw("s.nama,s.nik,s.tempat_lahir,s.tgl_lahir,wm.alamat_rumah,wm.rt,wm.rw,wm.kec_id,wm.kel_id,wm.kab_id,s.tgl_lahir,
						CASE WHEN s.verified_gakin is true THEN 'PRAMIS' ELSE '' END as status_pramis,
						CASE WHEN s.verified_gakin2 is true THEN 'GAKIN' ELSE '' END as status_gakin,
						CASE WHEN kec.kecamatan_dispenduk is not null THEN kec.kecamatan_dispenduk ELSE wm.kec_id END as kecamatan_dispenduk,
						CASE WHEN kel.kelurahan_dispenduk is not null THEN kel.kelurahan_dispenduk ELSE wm.kel_id END as kelurahan_dispenduk,
						wm.kab_id,
						se.npsn,se.nama as nama_sekolah
						")
            ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
            ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
            ->whereRaw("s.status_siswa='Aktif' AND s.alumni is not true")->whereBetween('tgl_lahir', [$awal, $akhir])
            ->whereIn('s.npsn', $npsn)->get();

          switch ($jenis) {
            case 'Jumlah siswa usia 5-6':
              $jumlah = collect($dt_awal);
              // code...
              break;
            case 'Jumlah siswa setelah survey usia 5-6':
              $jumlah = collect($dt_awal)
                ->filter(function ($user) {
                  return str_contains(strtolower($user->kab_id ?? ''), 'surabaya');
                });
              // code...
              break;
            case 'Jumlah siswa usia 5-6 sebelum survey (KK Bukan Surabaya)':
              $jumlah = collect($dt_awal)
                ->filter(function ($user) {
                  return !str_contains(strtolower($user->kab_id ?? ''), 'surabaya');
                });
              // code...
              break;

            default:
              // code...
              break;
          }
          // ->when($jenis=='Jumlah Total KK Surabaya',function($query){
          // 	return $query->whereRaw("wm.kab_id ilike '%surabaya%'");
          // })
          // ->when($jenis=='Jumlah Total KK Bukan Surabaya',function($query){
          // 	return $query->whereRaw("wm.kab_id not ilike '%surabaya%'");
          // })
          // ->when($jenis=='Jumlah Pramis',function($query){
          // 	return $query->whereRaw("s.verified_gakin is true");
          // })
          // ->when($jenis=='Jumlah Gamis',function($query){
          // 	return $query->whereRaw("s.verified_gakin2 is true");
          // })
          // ->when($jenis=='Jumlah siswa usia 5-6',function($query) use ($awal,$akhir){
          // 	return $query->whereRaw("s.tgl_lahir BETWEEN '$awal' AND '$akhir'");
          // })
          // ->when($jenis=='Jumlah siswa setelah survey usia 5-6',function($query) use ($awal,$akhir){
          // 	return $query->whereBetween('s.tgl_lahir',[$awal,$akhir]);
          // 	// ->whereRaw("wm.kab_id ilike '%surabaya%'");
          // })
          // ->when($jenis=='Jumlah siswa usia 5-6 sebelum survey (KK Bukan Surabaya)',function($query) use ($awal,$akhir){
          // 	return $query->whereRaw("(s.tgl_lahir BETWEEN '$awal' AND '$akhir') AND wm.kab_id not ilike '%surabaya%'");
          // })
          // ->when($jenis=='Jumlah Usia 5-6 tahun Pramis',function($query) use ($awal,$akhir){
          // 	return $query->whereRaw("(s.tgl_lahir BETWEEN '$awal' AND '$akhir') AND s.verified_gakin is true");
          // })
          // ->when($jenis=='Jumlah Usia 5-6 tahun Gamis',function($query) use ($awal,$akhir){
          // 	return $query->whereRaw("(s.tgl_lahir BETWEEN '$awal' AND '$akhir') AND s.verified_gakin2 is true");
          // })->get();
        } else if (in_array($jenis, [
          'Jumlah Penduduk usia 5-6'
        ])) {
          $jumlah_57 = DB::connection('pgsql_paud')->table('public.siswa as s')
            ->join('public.wali_murid as wm', function ($join) {
              return $join->on('wm.id_siswa', '=', 's.id_siswa')->on('wm.npsn', '=', 's.npsn');
            })
            ->join('public.sekolah as se', 'se.npsn', 's.npsn')
            ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
            ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
            ->selectRaw("s.nama,s.nik,s.tempat_lahir,s.tgl_lahir,wm.alamat_rumah as alamat,wm.rt,wm.rw,
						CASE WHEN kec.kecamatan_dispenduk is not null THEN kec.kecamatan_dispenduk ELSE wm.kec_id END as kecamatan,
						CASE WHEN kel.kelurahan_dispenduk is not null THEN kel.kelurahan_dispenduk ELSE wm.kel_id END as kelurahan
						")
            ->whereRaw("s.status_siswa='Aktif' AND s.alumni is not true")->whereBetween('tgl_lahir', [$awal, $akhir])
            ->whereIn('s.npsn', $npsn)->get();
          $jumlah_ats = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("nama,nik,tempat_lahir,tgl_lahir,alamat,rt,rw,kecamatan,kelurahan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("$where0")->get();

          // $jumlah_tdk_sekolah = collect($jumlah_ats)->filter(function($item){
          // 	return $item->status==='Tidak ada' && $item->status_alamat==='Sesuai' && $item->status_sekolah==='Tidak sekolah';
          // })->toArray();

          $jumlah_tdk_sekolah = collect($jumlah_ats)->filter(function ($item) {
            return $item->tandai_tidak_sekolah === 'ya';
          })->count();

          $tidak_dipakai = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("(status='Ada' OR status_sekolah='Sudah' OR status_sekolah='Akan sekolah')")
            ->whereRaw("$where0")->pluck('id')->toArray();
          $tidak_dipakai1 = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("tandai_tidak_sekolah='ya'")
            ->whereRaw("$where0")->pluck('id')->toArray();
          $id = array_merge($tidak_dipakai, $tidak_dipakai1);

          $jumlah_tdk_sesuai_kk = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("id,nik,nama,tempat_lahir,tgl_lahir,alamat,rt,rw,kelurahan,kecamatan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("$where0")->whereNotIn('id', $id)->get()->toArray();

          $dt_57 = collect($jumlah_57)->toArray();

          $jumlah = array_merge($jumlah_tdk_sekolah, $jumlah_tdk_sesuai_kk, $dt_57);


          // $jumlah_tdk_sekolah = collect($jumlah_ats)->filter(function($item){
          // 	return $item->status==='Tidak ada' && $item->status_alamat==='Sesuai' && $item->status_sekolah==='Tidak sekolah';
          // })->count();
          // $jumlah_tdk_sesuai_kk = collect($jumlah_ats)->filter(function($item){
          // 	return $item->status!=='Ada' && $item->status_alamat!=='Sesuai';
          // })->count();

          // $jumlah_total = $jumlah_usia57+$jumlah_tdk_sekolah+$jumlah_tidak_sesuai_kk;
        } else if (in_array($jenis, [
          'Jumlah siswa usia 5-6 sebelum survey (KK Surabaya)'
        ])) {
          $jumlah_57 = DB::connection('pgsql_paud')->table('public.siswa as s')
            ->join('public.wali_murid as wm', function ($join) {
              return $join->on('wm.id_siswa', '=', 's.id_siswa')->on('wm.npsn', '=', 's.npsn');
            })
            ->join('public.sekolah as se', 'se.npsn', 's.npsn')
            ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
            ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
            ->selectRaw("s.nama,s.nik,s.tempat_lahir,s.tgl_lahir,wm.alamat_rumah as alamat,wm.rt,wm.rw,wm.kab_id,
						CASE WHEN kec.kecamatan_dispenduk is not null THEN kec.kecamatan_dispenduk ELSE wm.kec_id END as kecamatan,
						CASE WHEN kel.kelurahan_dispenduk is not null THEN kel.kelurahan_dispenduk ELSE wm.kel_id END as kelurahan
						")
            ->whereRaw("s.status_siswa='Aktif' AND s.alumni is not true AND wm.kab_id ilike '%surabaya%'")->whereBetween('tgl_lahir', [$awal, $akhir])
            ->whereIn('s.npsn', $npsn)->count();
          $jumlah_ats = DB::connection('pgsql_paud')->table('history.anak_belum_sekolah')
            ->selectRaw("nama,nik,tempat_lahir,tgl_lahir,alamat,rt,rw,kecamatan,kelurahan,status,status_alamat,status_sekolah,status_gamis")
            ->whereRaw("$where0")->get();

          $jumlah_profil_ringkas = collect($jumlah_ats)->filter(function ($item) {
            return $item->status === 'Ada' || $item->status_sekolah === 'Sudah' || $item->status_sekolah === 'Akan sekolah';
          })->count();
          $limit = $jumlah_57 - $jumlah_profil_ringkas;

          $jumlah = DB::connection('pgsql_paud')->table('public.siswa as s')
            ->join('public.wali_murid as wm', function ($join) {
              return $join->on('wm.id_siswa', '=', 's.id_siswa')->on('wm.npsn', '=', 's.npsn');
            })
            ->join('public.sekolah as se', 'se.npsn', 's.npsn')
            ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'wm.kec_id')
            ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'wm.kel_id')
            ->selectRaw("s.nama,s.nik,s.tempat_lahir,s.tgl_lahir,wm.alamat_rumah as alamat,wm.rt,wm.rw,wm.kab_id,
						CASE WHEN kec.kecamatan_dispenduk is not null THEN kec.kecamatan_dispenduk ELSE wm.kec_id END as kecamatan,
						CASE WHEN kel.kelurahan_dispenduk is not null THEN kel.kelurahan_dispenduk ELSE wm.kel_id END as kelurahan
						")
            ->whereRaw("s.status_siswa='Aktif' AND s.alumni is not true")->whereBetween('tgl_lahir', [$awal, $akhir])
            ->whereIn('s.npsn', $npsn)->limit($limit)->get();

          // $jumlah_usia57_sby-$jumlah_profil_ringkas;
        }
        break;

      case 'lembaga':
        // 'Jumlah Lembaga',
        $jumlah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.nama,s.npsn,s.jenjang,s.status,s.alamat,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk")
          ->whereRaw("$where2 AND s.nama not ilike ALL(ARRAY['%terbuka%','%selalu%','WUSTHA%','ula%','pps%','pdf%','WUSTHO%']) AND u.user_enable is true")
          ->whereRaw("u.user_enable is true")
          ->whereIn('s.jenjang', ['TPA', 'SPS', 'RA', 'KB', 'TK'])
          ->when($jenis == 'Jumlah Lembaga TPA', function ($query) {
            return $query->whereRaw("s.jenjang='TPA'");
          })
          ->when($jenis == 'Jumlah Lembaga SPS/PPT', function ($query) {
            return $query->whereRaw("s.jenjang='SPS'");
          })
          ->when($jenis == 'Jumlah Lembaga KB', function ($query) {
            return $query->whereRaw("s.jenjang='KB'");
          })
          ->when($jenis == 'Jumlah Lembaga TK', function ($query) {
            return $query->whereRaw("s.jenjang='TK'");
          })
          ->when($jenis == 'Jumlah Lembaga RA', function ($query) {
            return $query->whereRaw("s.jenjang='RA'");
          });
        break;

      case 'pegawai':
        // 'Jumlah Pegawai',
        $npsn = collect($jumlah_lembaga)->pluck('npsn')->toArray();

        $jumlah = DB::connection('pgsql_paud')->table('public.pegawai as p')
          ->selectRaw("p.nama,p.no_ktp,p.alamat,p.rt,p.rw,p.kec_id,p.kelurahan,p.kab_id,p.induk,p.jabatan,p.pns,
					CASE WHEN kec.kecamatan_dispenduk is not null THEN kec.kecamatan_dispenduk ELSE p.kec_id END as kecamatan_dispenduk,
					CASE WHEN kel.kelurahan_dispenduk is not null THEN kel.kelurahan_dispenduk ELSE p.kelurahan END as kelurahan_dispenduk,
					p.kab_id,
					se.npsn,se.nama as nama_sekolah,j.nama as nama_jabatan
					")
          ->join('public.sekolah as se', 'se.npsn', 'p.npsn')
          ->leftjoin('public.jabatan_ptk as j', 'j.jabatan_ptk_id', DB::raw("CAST(p.jabatan as INTEGER)"))
          ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 'p.kec_id')
          ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 'p.kelurahan')
          ->whereRaw("p.keterangan='Aktif'")
          ->whereIn('p.npsn', $npsn)
          ->when($jenis == 'Jumlah Kepala Sekolah', function ($query) {
            return $query->where('jabatan', '2');
          })
          ->when($jenis == 'Jumlah Guru', function ($query) {
            return $query->whereIn('jabatan', ['1', '3', '4', '5', '7', '8', '9', '11', '12', '13', '19', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '46', '47', '48', '49', '50', '51', '52', '53']);
          })
          ->when($jenis == 'Jumlah Tendik', function ($query) {
            return $query->whereNotIn('jabatan', ['1', '2', '3', '4', '5', '7', '8', '9', '11', '12', '13', '19', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '46', '47', '48', '49', '50', '51', '52', '53']);
          })
          ->when($jenis == 'Jumlah Pegawai Induk', function ($query) {
            return $query->where('induk', 'true');
          })
          ->when($jenis == 'Jumlah Pegawai PNS', function ($query) {
            return $query->where('pns', '1');
          });
        break;

      default:
        $jumlah = [];
        break;
    }

    return DataTables::of($jumlah)->addIndexColumn()->make(true);
  }
}
