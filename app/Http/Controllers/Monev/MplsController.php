<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class MplsController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::table('cutoff_rekap.monev_bop')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);
        $jenjang = $request->jenjang;

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
          'jenjang' => $jenjang,
        ];
        return view('monev_baru.mpls.main', $data);
        break;

      case 'get_data':
        $tahun_monev = $request->tahun_monev;
        $status_monev = $request->status_monev;
        $jenjang = $request->jenjang;

        if ($tahun_monev == 2025) {
          $monev = DB::table('cutoff_rekap.monitoring_mpls_jawaban as jp')
            ->where('tahun', $tahun_monev)
            ->get();
        } else {
          $monev = DB::table('cutoff_rekap.monev_mpls_2026 as jp')
            ->where('tahun', $tahun_monev)
            ->get();
        }

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
          ->addColumn('aksi', function ($i) use ($npsn, $tahun_monev, $jenjang, $monev) {
            $dt_param = [
              'npsn' => $i->npsn,
              'tahun' => $tahun_monev,
            ];

            $param = base64_encode(json_encode($dt_param));

            $tombol = ' <a href="' . route('monev_baru-mpls', ['jenjang' => $jenjang, 'halaman' => 'form_monev', 'param' => $param]) . '" target="_blank" class="btn btn-outline-primary">Monev</a>';

            if (in_array($i->npsn, $npsn)) {
              $mon = collect($monev)->where('npsn', $i->npsn)->first();
              if ($mon->bunda_id == Auth::getUser()->id) {
                $tombol = ' <a href="' . route('monev_baru-mpls', ['jenjang' => $jenjang, 'halaman' => 'form_monev', 'param' => $param]) . '" target="_blank" class="btn btn-outline-primary">Monev</a>';
              } else {
                $tombol = '';
              }

              if ($tahun_monev <= 2025) {
                $tombol .= ' <a href="' . route('monev_baru-mpls', ['halaman' => 'cetak', 'jenjang' => $jenjang, 'param' => $param]) . '" target="_blank" class="btn btn-outline-success">Cetak Monev</a>';
              } else {
                $dt_param = array_merge($dt_param, ['cetak' => 'cetak']);
                $param = base64_encode(json_encode($dt_param));
                $tombol .= ' <a href="' . route('monev_baru-mpls', ['jenjang' => $jenjang, 'halaman' => 'form_monev', 'param' => $param]) . '" target="_blank" class="btn btn-outline-success">Cetak</a>';
              }
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
          ->rawColumns(['aksi', 'status_monev'])
          ->make(true);
        break;

      case 'form_monev':
        $jenjang = $request->jenjang;
        $param = json_decode(base64_decode($request->param));
        $npsn = $param->npsn;
        $tahun = $param->tahun;
        $cetak = (isset($param->cetak)) ? $param->cetak : '';
        $halaman = '';

        if ($jenjang == 'sd') {
          $conn = 'db_sd';
          $halaman = 'monev_paud';
        } else {
          $conn = 'pgsql_paud';
          $halaman = 'monev_paud';
        }

        $sekolah = DB::connection($conn)->table('public.sekolah as se')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'se.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 'se.desa')
          ->selectRaw("se.nama,se.npsn,se.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,se.jenjang,se.stempel")
          ->where('se.npsn', $npsn)
          ->first();

        $data_ks = DB::connection($conn)->table('public.pegawai as se')
          ->selectRaw("se.nama,se.npsn,se.hp")
          ->where('se.npsn', $npsn)
          ->where('se.keterangan', 'Aktif')
          ->where('se.jabatan', 2)
          ->first();

        $monev = DB::table('cutoff_rekap.monev_mpls_2026')->where(['npsn' => $npsn, 'tahun' => $tahun])->first();
        if (!empty($monev)) {
          $monev->catatan = json_decode($monev->catatan);
          $monev->identitas = json_decode($monev->identitas);
          $monev->indikator = json_decode($monev->indikator);
          $monev->gdriveLink = $monev->gdrivelink;
          $monev->disimpanPada = date('d-m-Y H:i:s', strtotime($monev->updated_at));
        }

        $bunda_kecamatan = DB::table('public.users as u')
          ->join('public.kecamatan as kel', 'kel.kecamatan_id', 'u.id_kecamatan')
          ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
          ->where('kel.kecamatan_dispenduk', $sekolah->kecamatan_dispenduk)
          ->where('p.id_jabatan', 18)
          ->whereNull('p.deleted_at')
          ->selectRaw("p.nama,p.email,p.nik,kel.kecamatan_dispenduk,kel.kecamatan_kode")
          ->first();

        $kode_kecamatan = $bunda_kecamatan->kecamatan_kode ?? '';

        $bunda_kelurahan = DB::table('public.users as u')
          ->join('public.kelurahan as kel', 'kel.kelurahan_id', 'u.id_kelurahan')
          ->join('public.profil_pokja as p', 'p.updated_by', 'u.id')
          ->where('kel.kelurahan_dispenduk', $sekolah->kelurahan_dispenduk)
          ->where('kel.kecamatan_kode', $kode_kecamatan)
          ->where('p.id_jabatan', 33)
          ->whereNull('p.deleted_at')
          ->selectRaw("p.nama,p.email,p.nik,kel.kelurahan_dispenduk")
          ->first();

        $ttd_bunda = DB::connection('db_cutt_off')->table('public.api_bunda_paud')
          ->whereIn('nik', [$bunda_kelurahan->nik, $bunda_kecamatan->nik])
          ->selectRaw("nik,link_ttd")
          ->get();

        $data = [
          'jenjang' => $jenjang,
          'sekolah' => $sekolah,
          'data_ks' => $data_ks,
          'tahun' => $tahun,
          'monev' => $monev,
          'bunda_kecamatan' => $bunda_kecamatan,
          'bunda_kelurahan' => $bunda_kelurahan,
          'ttd_bunda' => $ttd_bunda,
          'cetak' => $cetak,
        ];

        if ($tahun == 2025) {
          return view('monev_baru.mpls.modal_form', $data);
        } else {
          return view('monev_baru.mpls.2026.' . $halaman, $data);
        }
        break;

      case 'halaman_pertanyaan';
        $jenis_form = $request->jenis_form;
        $jenjang = $request->jenjang;
        $jenjang_sekolah = $request->jenjang_sekolah;
        $npsn = $request->npsn;
        $tahun = $request->tahun;
        $user_bunda = Auth::getUser()->id;

        switch ($jenis_form) {
          case 'A':
            $judul = '';
            $batas_urut = '10';
            break;
          case 'B':
            $judul = 'B. PERENCANAAN KEGIATAN';
            $batas_urut = '20';
            break;
          case 'C':
            $judul = 'C. PELAKSANAAN MPLS RAMAH (DAPAT DILAKUKAN KROSCEK DENGAN JADWAL MPLS SATUAN PENDIDIKAN)';
            $batas_urut = '30';
            break;
          case 'C1':
            if ($jenjang == 'sd') {
              $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - SEKOLAHKU MENGGEMBIRAKAN';
            } else {
              $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - PAUDKU MENGGEMBIRAKAN';
            }
            $batas_urut = '40';
            break;
          case 'C2':
            $judul = 'C.2. PELAKSANAAN MPLS RAMAH HARI 2 - AKU MENGENAL TEMAN DAN GURU';
            $batas_urut = '50';
            break;
          case 'C3':
            if ($jenjang == 'sd') {
              $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BERBAGI CERITA';
            } else {
              $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BISA MENJAGA DIRI DAN BARANGKU';
            }
            $batas_urut = '60';
            break;
          case 'C4':
            if ($jenjang == 'sd') {
              $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU BERSIH DAN SEHAT';
            } else {
              $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU DAN TEMANKU, HEBAT';
            }
            $batas_urut = '70';
            break;
          case 'C5':
            if ($jenjang == 'sd') {
              $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI SEKOLAH';
            } else {
              $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI PAUD';
            }
            $batas_urut = '80';
            break;
          case 'D':
            $judul = 'D. LINGKUNGAN, KESEHATAN, & KEAMANAN';
            $batas_urut = '90';
            break;
          case 'E':
            $judul = 'E. PARTISIPASI ORANG TUA & LINGKUNGAN';
            $batas_urut = '100';
            break;
          case 'F':
            $judul = 'F. PERLINDUNGAN ANAK';
            $batas_urut = '110';
            break;
          case 'G':
            $judul = 'G. PERAN DAN SOSIALISASI BUNDA PAUD';
            $batas_urut = '120';
            break;
          case 'H':
            $judul = 'H. DUKUNGAN DAN TINDAK LANJUT';
            $batas_urut = '130';
            break;

          default:
            $judul = '';
            $batas_urut = '10';
            break;
        }

        $pertanyaan_identitas = DB::table('cutoff_rekap.monitoring_mpls')->whereRaw("jenjang ilike '%" . $jenjang_sekolah . "%' AND tipe='satdik2026' AND urutan BETWEEN " . ($batas_urut - 10) . " AND $batas_urut")
          ->selectRaw("*,CASE WHEN (parent_id is null) THEN id ELSE parent_id END as ortu")
          ->orderByRaw("CAST(urutan as INTEGER) ASC,nomor ASC")->get();

        $get_jawaban = DB::table('cutoff_rekap.monitoring_mpls_jawaban')->whereRaw("tipe='satdik2026' AND npsn='$npsn' AND tahun='$tahun'")->first();
        $jawaban = [];
        if (!empty($get_jawaban)) {
          $jawaban = json_decode($get_jawaban->jawaban);
          if (isset($jawaban->$jenis_form)) {
            $jawaban = collect($jawaban->$jenis_form)->toArray();
          } else {
            $jawaban = [];
          }
        }

        $data = [
          'judul' => $judul,
          'pertanyaan_identitas' => $pertanyaan_identitas,
          'jenis_form' => $jenis_form,
          'npsn' => $npsn,
          'jawaban' => $jawaban,
          'tahun' => $tahun,
        ];

        $content = view('monev_baru.mpls.pertanyaan', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        $data = $request->all();
        $npsn = $request->sekolah;
        $tahun = $request->tahun;
        $jenis_form = $request->jenis_form;
        $user_bunda = Auth::getUser()->id;

        unset($data['sekolah']);
        unset($data['jenis_form']);
        unset($data['tahun']);

        $data_simpan = [
          'tipe' => 'satdik2026',
          'monitoring_mpls_id' => '0',
          'npsn' => $npsn,
          'user_id' => $user_bunda,
          'updated_at' => date('Y-m-d H:i:s'),
        ];

        $get_jawaban = DB::table('cutoff_rekap.monitoring_mpls_jawaban')->whereRaw("tipe='satdik2026' AND npsn='$npsn' AND tahun='$tahun'")->first();

        $jawaban = [];
        if (!empty($get_jawaban)) {
          $jawaban = json_decode($get_jawaban->jawaban);
          $jawaban = collect($jawaban)->toArray();
          if (isset($jawaban[$jenis_form])) {
            $jawaban[$jenis_form] = $data;
          } else {
            $jawaban = array_merge($jawaban, [$jenis_form => $data]);
          }
        } else {
          $jawaban = [$jenis_form => $data];
        }

        $jawaban = json_encode($jawaban);

        $data_simpan = array_merge($data_simpan, ['jawaban' => $jawaban]);
        $where_update = [
          'tipe' => 'satdik2026',
          'npsn' => $npsn,
          'tahun' => $tahun,
        ];

        $simpan = DB::table('cutoff_rekap.monitoring_mpls_jawaban')->updateOrInsert($where_update, $data_simpan);
        if ($simpan) {
          return ['code' => 200];
        } else {
          return ['code' => 250];
        }
        break;

      case 'cetak':
        // http://localhost/sibunda/public/cetak_monitoring_mpls?param=eyJ1c2VyX2lkIjoiMjIiLCJucHNuIjoiMjA1MzM0OTQiLCJqZW5qYW5nIjoic2QifQ==
        $param = json_decode(base64_decode($request->param));
        $npsn = $param->npsn;
        $jenjang = $request->jenjang;
        $tahun = $param->tahun;
        $user_bunda = Auth::getUSer()->id;

        if ($jenjang == 'sd') {
          $conn = 'db_sd';
          $arr_jenjang = "'SD','MI'";
        } else {
          $conn = 'pgsql_paud';
          $arr_jenjang = "'SPS','TPA','KB','TK','RA'";
        }

        $sekolah = DB::connection($conn)->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.nama,s.npsn,kec.kecamatan_dispenduk as kecamatan,kel.kelurahan_dispenduk as kelurahan,s.kepala as ks,s.jenjang")
          ->whereRaw("u.user_enable is true AND s.jenjang in ($arr_jenjang) AND s.npsn='$npsn'")
          ->first();
        $user = DB::table('public.users')->selectRaw("id,nama")->where('id', $user_bunda)->first();
        $mpls = DB::table('cutoff_rekap.monitoring_mpls_jawaban')->whereRaw("npsn='$npsn' AND tahun='$tahun'")->first();

        $data = [
          'mpls' => $mpls,
          'user' => $user,
          'sekolah' => $sekolah,
          'jenjang' => $jenjang,
          'tahun' => $tahun,
        ];

        return view('monev_baru.mpls.cetak', $data);
        break;

      case 'cetak_identitas':
        $jenis_form = $request->jenis_form;
        $jenjang = $request->jenjang;
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        if ($jenjang == 'sd') {
          $conn = 'db_sd';
          $arr_jenjang = "'SD','MI'";
        } else {
          $conn = 'pgsql_paud';
          $arr_jenjang = "'SPS','TPA','KB','TK','RA'";
        }

        $sekolah = DB::connection($conn)->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.nama,s.npsn,kec.kecamatan_dispenduk as kecamatan,kel.kelurahan_dispenduk as kelurahan,s.kepala as ks,s.jenjang")
          ->whereRaw("u.user_enable is true AND s.jenjang in ($arr_jenjang) AND s.npsn='$npsn'")
          ->first();

        switch ($jenis_form) {
          case 'A':
            $judul = '';
            $batas_urut = '10';
            break;
          case 'B':
            $judul = 'B. PERENCANAAN KEGIATAN';
            $batas_urut = '20';
            break;
          case 'C':
            $judul = 'C. PELAKSANAAN MPLS RAMAH (DAPAT DILAKUKAN KROSCEK DENGAN JADWAL MPLS SATUAN PENDIDIKAN)';
            $batas_urut = '30';
            break;
          case 'C1':
            if ($jenjang == 'sd') {
              $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - SEKOLAHKU MENGGEMBIRAKAN';
            } else {
              $judul = 'C.1. PELAKSANAAN MPLS RAMAH HARI 1 - PAUDKU MENGGEMBIRAKAN';
            }
            $batas_urut = '40';
            break;
          case 'C2':
            $judul = 'C.2. PELAKSANAAN MPLS RAMAH HARI 2 - AKU MENGENAL TEMAN DAN GURU';
            $batas_urut = '50';
            break;
          case 'C3':
            if ($jenjang == 'sd') {
              $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BERBAGI CERITA';
            } else {
              $judul = 'C.3. PELAKSANAAN MPLS RAMAH HARI 3 - AKU BISA MENJAGA DIRI DAN BARANGKU';
            }
            $batas_urut = '60';
            break;
          case 'C4':
            if ($jenjang == 'sd') {
              $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU BERSIH DAN SEHAT';
            } else {
              $judul = 'C.4. PELAKSANAAN MPLS RAMAH HARI 4 - AKU DAN TEMANKU, HEBAT';
            }
            $batas_urut = '70';
            break;
          case 'C5':
            if ($jenjang == 'sd') {
              $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI SEKOLAH';
            } else {
              $judul = 'C.5. PELAKSANAAN MPLS RAMAH HARI 5 - CERIA BERSAMA DI PAUD';
            }
            $batas_urut = '80';
            break;
          case 'D':
            $judul = 'D. LINGKUNGAN, KESEHATAN, & KEAMANAN';
            $batas_urut = '90';
            break;
          case 'E':
            $judul = 'E. PARTISIPASI ORANG TUA & LINGKUNGAN';
            $batas_urut = '100';
            break;
          case 'F':
            $judul = 'F. PERLINDUNGAN ANAK';
            $batas_urut = '110';
            break;
          case 'G':
            $judul = 'G. PERAN DAN SOSIALISASI BUNDA PAUD';
            $batas_urut = '120';
            break;
          case 'H':
            $judul = 'H. DUKUNGAN DAN TINDAK LANJUT';
            $batas_urut = '130';
            break;

          default:
            $judul = '';
            $batas_urut = '10';
            break;
        }

        $pertanyaan_identitas = DB::table('cutoff_rekap.monitoring_mpls')->whereRaw("jenjang ilike '%" . $sekolah->jenjang . "%' AND tipe='satdik2026' AND urutan BETWEEN " . ($batas_urut - 10) . " AND $batas_urut")
          ->selectRaw("*,CASE WHEN (parent_id is null) THEN id ELSE parent_id END as ortu")
          ->orderByRaw("CAST(urutan as INTEGER) ASC,nomor ASC")->get();

        $get_jawaban = DB::table('cutoff_rekap.monitoring_mpls_jawaban')->whereRaw("tipe='satdik2026' AND npsn='$npsn' AND tahun='$tahun'")->first();
        $jawaban = [];
        if (!empty($get_jawaban)) {
          $jawaban = json_decode($get_jawaban->jawaban);
          if (isset($jawaban->$jenis_form)) {
            $jawaban = collect($jawaban->$jenis_form)->toArray();
          } else {
            $jawaban = [];
          }
        }

        $data = [
          'judul' => $judul,
          'pertanyaan_identitas' => $pertanyaan_identitas,
          'jenis_form' => $jenis_form,
          'npsn' => $npsn,
          'jawaban' => $jawaban,
        ];
        $content = view('monev_baru.mpls.cetak_identitas', $data)->render();

        return ['content' => $content];
        break;

      case 'simpan_monev_26':
        // return $request->all();
        $data_insert = $request->all();
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $data_insert['catatan'] = json_encode($request->catatan);
        $data_insert['indikator'] = json_encode($request->indikator);
        $data_insert['identitas'] = json_encode($request->identitas);
        unset($data_insert['gdriveLink']);
        unset($data_insert['disimpanPada']);
        $data_insert['gdrivelink'] = $request->gdriveLink;
        $data_insert['updated_at'] = date('Y-m-d H:i:s');
        $data_insert['bunda_id'] = Auth::getUser()->id;

        $simpan = DB::table('cutoff_rekap.monev_mpls_2026')->updateOrInsert(['npsn' => $npsn, 'tahun' => $tahun], $data_insert);
        if ($simpan) {
          return ['code' => '200'];
        }
        return ['code' => '250'];
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }
}
