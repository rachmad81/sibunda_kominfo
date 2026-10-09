<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Bunda\LombaController as LombaBunda;
use App\Http\Libraries\UraianUtamaLomba;
use App\Http\Libraries\UraianPendukungLomba;

use DB;

class ApiRekapController extends Controller
{
  function lomba(Request $request)
  {
    $jenis = isset($request->jenis) ? $request->jenis : '-';

    switch ($jenis) {
      case 'lomba':
        $tahun = isset($request->tahun) ? $request->tahun : date('Y');

        $user = DB::select("SELECT peserta.user_id FROM cutoff_rekap.peserta_lomba AS peserta
					left JOIN cutoff_rekap.berkas_identitas AS berkas ON berkas.user_id=peserta.user_id AND berkas.tahun='$tahun'
					left JOIN cutoff_rekap.kriteria_utama AS utama ON utama.user_id=peserta.user_id AND utama.tahun='$tahun'
					left JOIN cutoff_rekap.kriteria_pendukung AS pendukung ON pendukung.user_id=peserta.user_id AND pendukung.tahun='$tahun'
					WHERE peserta.tahun='$tahun'");
        $utama = UraianUtamaLomba::cek_tahun($tahun);
        $pendukung = UraianPendukungLomba::cek_tahun($tahun);

        $data = [
          'user' => $user,
          'tahun' => $tahun,
          'utama' => $utama,
          'pendukung' => $pendukung,
        ];

        return view('rekap_api.lomba.main', $data);
        break;

      case 'lomba-data':
        $user_id = $request->user_id;
        $tahun = $request->tahun;

        $peserta = DB::select("SELECT *,peserta.nama as nama_peserta,COALESCE(u.id_kecamatan,0) as id_kecamatan,COALESCE(u.id_kelurahan,0) as id_kelurahan FROM cutoff_rekap.peserta_lomba AS peserta
					left JOIN cutoff_rekap.berkas_identitas AS berkas ON berkas.user_id=peserta.user_id AND berkas.tahun='$tahun'
					left JOIN cutoff_rekap.kriteria_utama AS utama ON utama.user_id=peserta.user_id AND utama.tahun='$tahun'
					left JOIN cutoff_rekap.kriteria_pendukung AS pendukung ON pendukung.user_id=peserta.user_id AND pendukung.tahun='$tahun'
					left JOIN public.users AS u ON u.id=peserta.user_id
					WHERE peserta.tahun='$tahun' AND peserta.user_id='$user_id'");

        $utama = UraianUtamaLomba::cek_tahun($tahun);
        $pendukung = UraianPendukungLomba::cek_tahun($tahun);

        $peserta = collect($peserta)->first();

        $kegiatan = DB::table('public.kegiatan as keg')
          ->join('public.program as p', 'p.id_program', 'keg.program_id')
          ->whereRaw("p.tahun='$tahun' AND (COALESCE(p.id_kecamatan,0)='$peserta->id_kecamatan' AND COALESCE(p.id_kelurahan,0)='$peserta->id_kelurahan')")->get();

        $mitra = DB::table('cutoff_rekap.mitra_daftar')->where('deleted_at', null)->get();

        $mitra_apresiasi = DB::table('cutoff_rekap.mitra_apresiasi')->where('deleted_at', null)->where('user_id', $user_id)->get();
        $kegiatan_apresiasi = DB::table('cutoff_rekap.kegiatan_apresiasi')->where('deleted_at', null)->where('user_id', $user_id)->get();

        $data = [
          'd' => $peserta,
          'kegiatan' => $kegiatan,
          'mitra' => $mitra,
          'mitra_apresiasi' => $mitra_apresiasi,
          'kegiatan_apresiasi' => $kegiatan_apresiasi,
          'utama' => $utama,
          'pendukung' => $pendukung,
        ];

        $content = view('rekap_api.lomba.data', $data)->render();
        return ['content' => $content];
        break;

      default:
        return 'Sorry, you are ugly';
        break;
    }
  }

  function nilai(Request $request)
  {
    $jenis = isset($request->jenis) ? $request->jenis : '-';
    $tahun = $request->tahun;

    if ($tahun == '2024') {
      switch ($jenis) {
        case 'nilai':
          $user = DB::select("SELECT peserta.user_id FROM cutoff_rekap.peserta_lomba AS peserta
						left JOIN cutoff_rekap.berkas_identitas AS berkas ON berkas.user_id=peserta.user_id AND berkas.tahun='$tahun'
						WHERE peserta.tahun='$tahun'");
          $penilai = DB::select("SELECT nama FROM public.users WHERE role='penilai' order by username ASC");

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai.' . $tahun . '.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")
            ->first();
          $penilai = DB::table('users')->where('role', 'penilai')->orderBy('username')->get();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();

          $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
          $nama_utama = collect($uraian_utama)->pluck('name');
          $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
          $nama_pendukung = collect($uraian_pendukung)->pluck('name');

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;
            $nilai_angka_pendukung = 0;
            $nilai_angka_selesai_pendukung = 0;

            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                for ($i = 0; $i < count($nama_utama); $i++) {
                  $kolom = 'nilai_' . $nama_utama[$i];
                  if (!is_null($penilaian_utama->$kolom)) {
                    $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    if ($penilaian_utama->status == 'Selesai') {
                      $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            if (!empty($nilai_pendukung)) {
              $penilaian_pendukung =  collect($nilai_pendukung)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_pendukung)) {
                for ($i = 0; $i < count($nama_pendukung); $i++) {
                  $kolom = 'nilai_' . $nama_pendukung[$i];
                  if (!is_null($penilaian_pendukung->$kolom)) {
                    $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    if ($penilaian_pendukung->status == 'Selesai') {
                      $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_pendukung)) {
                $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
                if ($penilaian_pendukung->status == 'Selesai') {
                  $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_pendukung = $status_pendukung;
              } else {
                $p->status_pendukung = 'Belum dinilai';
              }
            }

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_pendukung = $nilai_angka_pendukung;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $p->jumlah_pendukung_selesai = $nilai_angka_selesai_pendukung;
            $nilai_total += (($nilai_angka_selesai_utama * 4) + ($nilai_angka_selesai_pendukung * 3));
          }

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update(['nilai_akhir_tahap1' => $nilai_total]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai.' . $tahun . '.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    } else if ($tahun == '2025') {
      $penilai = DB::select("SELECT id,nama FROM public.users WHERE role='penilai' AND verified is true
				order by
				CASE id
					WHEN 907 THEN 0
					WHEN 1006 THEN 1
					WHEN 947 THEN 2
					WHEN 1005 THEN 3
					WHEN 908 THEN 4
					WHEN 1008 THEN 5
					WHEN 911 THEN 6
					WHEN 1007 THEN 7
					ELSE 8
				END
				");

      switch ($jenis) {
        case 'nilai':
          $kelompok = ['Kelompok A', 'Kelompok B', 'Kelompok C', 'Kelompok D'];
          $data = [
            'kelompok' => $kelompok,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai.' . $tahun . '.main', $data);
          break;

        case 'nilai-data':
          $user_id_kelompok = $request->user_id;
          $no = $request->i;

          if ($user_id_kelompok == 'Kelompok A') {
            $id_penilai = [907, 1006];
            $kec_kelompok = [
              'ASEM ROWO',
              'BENOWO',
              'BUBUTAN',
              'BULAK',
              'DUKUH PAKIS',
              'GAYUNGAN',
              'GENTENG',
              'GUBENG',
              'GUNUNG ANYAR',
            ];
          } else if ($user_id_kelompok == 'Kelompok B') {
            $id_penilai = [947, 1005];
            $kec_kelompok = [
              'JAMBANGAN',
              'KARANG PILANG',
              'KENJERAN',
              'KREMBANGAN',
              'LAKARSANTRI',
              'MULYOREJO',
              'PABEAN CANTIAN',
              'PAKAL',
            ];
          } else if ($user_id_kelompok == 'Kelompok C') {
            $id_penilai = [908, 1008];
            $kec_kelompok = [
              'kec_instansi',
              'RUNGKUT',
              'SAMBIKEREP',
              'SAWAHAN',
              'SEMAMPIR',
              'SIMOKERTO',
              'SUKOLILO',
              'SUKOMANUNGGAL',
            ];
          } else if ($user_id_kelompok == 'Kelompok D') {
            $id_penilai = [911, 1007];
            $kec_kelompok = [
              'TAMBAKSARI',
              'TANDES',
              'TEGALSARI',
              'TENGGILIS MEJOYO',
              'WIYUNG',
              'WONOCOLO',
              'WONOKROMO',
            ];
          }

          $kelompok = DB::table('public.kelurahan as kel')
            ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->select('kelurahan_dispenduk')->whereIn('kec.kecamatan_dispenduk', $kec_kelompok)->get();
          $kel_kelompok = collect($kelompok)->pluck('kelurahan_dispenduk')->toArray();

          $d_peserta_lomba = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun'")->where(function ($mana) use ($kel_kelompok, $kec_kelompok) {
              $mana->whereIn('kel_instansi', $kel_kelompok)->orWhereIn('kec_instansi', $kec_kelompok);
            })->orderByRaw("CASE WHEN kec_instansi is NULL THEN 1 ELSE 0 END ASC, kec_instansi ASC,CASE WHEN kel_instansi is NULL THEN 1 ELSE 0 END ASC, kel_instansi ASC")->get();

          $user_id = collect($d_peserta_lomba)->pluck('user_id')->toArray();

          $d_identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun'")
            ->whereIn('user_id', $user_id)
            ->get();

          $d_nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->orderBy('user_id', 'ASC')->get();
          $d_nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->orderBy('user_id', 'ASC')->get();
          $d_kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->get();
          $d_kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->get();

          $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
          $nama_utama = collect($uraian_utama)->pluck('name');
          $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
          $nama_pendukung = collect($uraian_pendukung)->pluck('name');

          $nomor = 0;
          $d_penilai = collect($penilai)->whereIn('id', $id_penilai);;

          foreach ($d_peserta_lomba as $d) {
            $d->no = ($nomor + 1);

            $identitas = collect($d_identitas)->where('user_id', $d->user_id)->first();
            $kriteria_utama = collect($d_kriteria_utama)->where('user_id', $d->user_id)->first();
            $kriteria_pendukung = collect($d_kriteria_pendukung)->where('user_id', $d->user_id)->first();

            $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';
            if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
              $status_utama = $kriteria_utama->status;
            } else {
              $status_utama = 'Belum diajukan oleh bunda';
            }
            if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
              $status_pendukung = $kriteria_pendukung->status;
            } else {
              $status_pendukung = 'Belum diajukan oleh bunda';
            }

            $nilai_total = 0;
            $arr_penilai = [];
            foreach ($d_penilai as $p) {
              $nilai_utama = collect($d_nilai_utama)->where('user_id', $d->user_id)->where('penilai_id', $p->id)->first();
              $nilai_pendukung = collect($d_nilai_pendukung)->where('user_id', $d->user_id)->where('penilai_id', $p->id)->first();

              $nilai_angka_utama = 0;
              $nilai_angka_selesai_utama = 0;
              $nilai_angka_pendukung = 0;
              $nilai_angka_selesai_pendukung = 0;

              if (!empty($nilai_utama)) {
                $penilaian_utama = $nilai_utama;
                if (!empty($penilaian_utama)) {
                  for ($i = 0; $i < count($nama_utama); $i++) {
                    $kolom = 'nilai_' . $nama_utama[$i];
                    if (!is_null($penilaian_utama->$kolom)) {
                      $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                      if ($penilaian_utama->status == 'Selesai') {
                        $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                      }
                    }
                  }
                }
                if (!empty($penilaian_utama)) {
                  $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                  if ($penilaian_utama->status == 'Selesai') {
                    $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                  }
                  $p->status_utama = $status_utama;
                } else {
                  $p->status_utama = 'Belum dinilai';
                }
              }

              if (!empty($nilai_pendukung)) {
                $penilaian_pendukung = $nilai_pendukung;
                if (!empty($penilaian_pendukung)) {
                  for ($i = 0; $i < count($nama_pendukung); $i++) {
                    $kolom = 'nilai_' . $nama_pendukung[$i];
                    if (!is_null($penilaian_pendukung->$kolom)) {
                      $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                      if ($penilaian_pendukung->status == 'Selesai') {
                        $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                      }
                    }
                  }
                }
                if (!empty($penilaian_pendukung)) {
                  $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
                  if ($penilaian_pendukung->status == 'Selesai') {
                    $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                  }
                  $p->status_pendukung = $status_pendukung;
                } else {
                  $p->status_pendukung = 'Belum dinilai';
                }
              }

              $nilai_total += (($nilai_angka_selesai_utama * 4) + ($nilai_angka_selesai_pendukung * 2));

              $d_hasil_penilaian = [
                'penilai_id' => $p->id,
                'jumlah_utama' => $nilai_angka_utama,
                'jumlah_pendukung' => $nilai_angka_pendukung,
                'jumlah_utama_selesai' => $nilai_angka_selesai_utama,
                'jumlah_pendukung_selesai' => $nilai_angka_selesai_pendukung,
                'nilai_total' => $nilai_total,
              ];
              array_push($arr_penilai, $d_hasil_penilaian);
            }

            $d->penilai = $arr_penilai;

            $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
              ->whereRaw("tahun='$tahun' and user_id='$d->user_id'")->update(['nilai_akhir_tahap1' => $nilai_total]);
          }

          $data = [
            'kelompok' => $user_id_kelompok,
            'penilai' => $penilai,
            'pesertas' => $d_peserta_lomba,
          ];

          $content = view('rekap_api.nilai.' . $tahun . '.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    } else if ($tahun == '2026') {
      $penilai = DB::select("SELECT id,nama FROM public.users WHERE role='penilai' AND verified is true
				order by
				CASE id
					WHEN 908 THEN 0
					WHEN 1008 THEN 1
					WHEN 911 THEN 2
					WHEN 1007 THEN 3
					WHEN 907 THEN 4
					WHEN 1006 THEN 5
					WHEN 947 THEN 6
					WHEN 1076 THEN 7
					ELSE 8
				END
				");

      switch ($jenis) {
        case 'nilai':
          $kelompok = ['Kelompok A', 'Kelompok B', 'Kelompok C', 'Kelompok D'];
          $data = [
            'kelompok' => $kelompok,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai.' . $tahun . '.main', $data);
          break;

        case 'nilai-data':
          $user_id_kelompok = $request->user_id;
          $no = $request->i;

          if ($user_id_kelompok == 'Kelompok A') {
            $id_penilai = [908, 1008];
            $kec_kelompok = [
              'ASEM ROWO',
              'BENOWO',
              'BUBUTAN',
              'BULAK',
              'DUKUH PAKIS',
              'GAYUNGAN',
              'GENTENG',
              'GUBENG',
              'GUNUNG ANYAR',
            ];
          } else if ($user_id_kelompok == 'Kelompok B') {
            $id_penilai = [911, 1007];
            $kec_kelompok = [
              'JAMBANGAN',
              'KARANG PILANG',
              'KENJERAN',
              'KREMBANGAN',
              'LAKARSANTRI',
              'MULYOREJO',
              'PABEAN CANTIAN',
              'PAKAL',
            ];
          } else if ($user_id_kelompok == 'Kelompok C') {
            $id_penilai = [907, 1006];
            $kec_kelompok = [
              'kec_instansi',
              'RUNGKUT',
              'SAMBIKEREP',
              'SAWAHAN',
              'SEMAMPIR',
              'SIMOKERTO',
              'SUKOLILO',
              'SUKOMANUNGGAL',
            ];
          } else if ($user_id_kelompok == 'Kelompok D') {
            $id_penilai = [947, 1076];
            $kec_kelompok = [
              'TAMBAKSARI',
              'TANDES',
              'TEGALSARI',
              'TENGGILIS MEJOYO',
              'WIYUNG',
              'WONOCOLO',
              'WONOKROMO',
            ];
          }

          $kelompok = DB::table('public.kelurahan as kel')
            ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 'kel.kecamatan_kode')->select('kelurahan_dispenduk')->whereIn('kec.kecamatan_dispenduk', $kec_kelompok)->get();
          $kel_kelompok = collect($kelompok)->pluck('kelurahan_dispenduk')->toArray();

          $d_peserta_lomba = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun'")->where(function ($mana) use ($kel_kelompok, $kec_kelompok) {
              $mana->whereIn('kel_instansi', $kel_kelompok)->orWhereIn('kec_instansi', $kec_kelompok);
            })->orderByRaw("CASE WHEN kec_instansi is NULL THEN 1 ELSE 0 END ASC, kec_instansi ASC,CASE WHEN kel_instansi is NULL THEN 1 ELSE 0 END ASC, kel_instansi ASC")->get();

          $user_id = collect($d_peserta_lomba)->pluck('user_id')->toArray();

          $d_identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun'")
            ->whereIn('user_id', $user_id)
            ->get();

          $d_nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->orderBy('user_id', 'ASC')->get();
          $d_nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->orderBy('user_id', 'ASC')->get();
          $d_kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->get();
          $d_kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
            ->whereRaw("tahun='$tahun'")->whereIn('user_id', $user_id)->get();

          $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
          $nama_utama = collect($uraian_utama)->pluck('name');
          $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
          $nama_pendukung = collect($uraian_pendukung)->pluck('name');

          $nomor = 0;
          $d_penilai = collect($penilai)->whereIn('id', $id_penilai);;

          foreach ($d_peserta_lomba as $d) {
            $d->no = ($nomor + 1);

            $identitas = collect($d_identitas)->where('user_id', $d->user_id)->first();
            $kriteria_utama = collect($d_kriteria_utama)->where('user_id', $d->user_id)->first();
            $kriteria_pendukung = collect($d_kriteria_pendukung)->where('user_id', $d->user_id)->first();

            $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';
            if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
              $status_utama = $kriteria_utama->status;
            } else {
              $status_utama = 'Belum diajukan oleh bunda';
            }
            if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
              $status_pendukung = $kriteria_pendukung->status;
            } else {
              $status_pendukung = 'Belum diajukan oleh bunda';
            }

            $nilai_total = 0;
            $arr_penilai = [];
            foreach ($d_penilai as $p) {
              $nilai_utama = collect($d_nilai_utama)->where('user_id', $d->user_id)->where('penilai_id', $p->id)->first();
              $nilai_pendukung = collect($d_nilai_pendukung)->where('user_id', $d->user_id)->where('penilai_id', $p->id)->first();

              $nilai_angka_utama = 0;
              $nilai_angka_selesai_utama = 0;
              $nilai_angka_pendukung = 0;
              $nilai_angka_selesai_pendukung = 0;

              if (!empty($nilai_utama)) {
                $penilaian_utama = $nilai_utama;
                if (!empty($penilaian_utama)) {
                  for ($i = 0; $i < count($nama_utama); $i++) {
                    $kolom = 'nilai_' . $nama_utama[$i];
                    if (!is_null($penilaian_utama->$kolom)) {
                      $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                      if ($penilaian_utama->status == 'Selesai') {
                        $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                      }
                    }
                  }
                }
                if (!empty($penilaian_utama)) {
                  $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                  if ($penilaian_utama->status == 'Selesai') {
                    $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                  }
                  $p->status_utama = $status_utama;
                } else {
                  $p->status_utama = 'Belum dinilai';
                }
              }

              if (!empty($nilai_pendukung)) {
                $penilaian_pendukung = $nilai_pendukung;
                if (!empty($penilaian_pendukung)) {
                  for ($i = 0; $i < count($nama_pendukung); $i++) {
                    $kolom = 'nilai_' . $nama_pendukung[$i];
                    if (!is_null($penilaian_pendukung->$kolom)) {
                      $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                      if ($penilaian_pendukung->status == 'Selesai') {
                        $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                      }
                    }
                  }
                }
                if (!empty($penilaian_pendukung)) {
                  $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
                  if ($penilaian_pendukung->status == 'Selesai') {
                    $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                  }
                  $p->status_pendukung = $status_pendukung;
                } else {
                  $p->status_pendukung = 'Belum dinilai';
                }
              }

              $nilai_total += (($nilai_angka_selesai_utama * 4) + ($nilai_angka_selesai_pendukung * 2));

              $d_hasil_penilaian = [
                'penilai_id' => $p->id,
                'jumlah_utama' => $nilai_angka_utama,
                'jumlah_pendukung' => $nilai_angka_pendukung,
                'jumlah_utama_selesai' => $nilai_angka_selesai_utama,
                'jumlah_pendukung_selesai' => $nilai_angka_selesai_pendukung,
                'nilai_total' => $nilai_total,
              ];
              array_push($arr_penilai, $d_hasil_penilaian);
            }

            $d->penilai = $arr_penilai;

            $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
              ->whereRaw("tahun='$tahun' and user_id='$d->user_id'")->update(['nilai_akhir_tahap1' => $nilai_total]);
          }

          $data = [
            'kelompok' => $user_id_kelompok,
            'penilai' => $penilai,
            'pesertas' => $d_peserta_lomba,
          ];

          $content = view('rekap_api.nilai.' . $tahun . '.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    }
  }

  function nilai2(Request $request)
  {
    $jenis = isset($request->jenis) ? $request->jenis : '-';
    $tahun = $request->tahun;

    if ($tahun == '2024') {
      switch ($jenis) {
        case 'nilai':
          $tahun = isset($request->tahun) ? $request->tahun : date('Y');

          $user = $data_kec = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('9')
            ->get();

          $data_kel = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('12')
            ->get();

          $user = array_merge($data_kec->toArray(), $data_kel->toArray());

          $penilai = DB::select("SELECT nama FROM public.users WHERE role='penilai' order by username ASC");

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai2.2024.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $tahun = $request->tahun;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $penilai = DB::table('users')->where('role', 'penilai')->orderBy('username')->get();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;

            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                $kolom = 'hasil';
                if (!is_null($penilaian_utama->$kolom)) {
                  $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
                  if ($penilaian_utama->status == 'Selesai') {
                    $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $nilai_total += $nilai_angka_selesai_utama;
          }

          $nilai_total *= 10;

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update(['nilai_akhir_tahap2' => $nilai_total]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai2.2024.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    } else if ($tahun == '2025') {
      $penilai = DB::select("SELECT id,nama FROM public.users WHERE role='penilai' AND verified is true
				order by
				CASE id
					WHEN 907 THEN 0
					WHEN 1006 THEN 1
					WHEN 947 THEN 2
					WHEN 1005 THEN 3
					WHEN 908 THEN 4
					WHEN 1008 THEN 5
					WHEN 911 THEN 6
					WHEN 1007 THEN 7
					ELSE 8
				END
				");

      switch ($jenis) {
        case 'nilai':
          $tahun = isset($request->tahun) ? $request->tahun : date('Y');

          $user = $data_kec = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('9')
            ->get();

          $data_kel = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('12')
            ->get();

          $user = array_merge($data_kec->toArray(), $data_kel->toArray());

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai2.2025.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $tahun = $request->tahun;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;

            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                $kolom = 'hasil';
                if (!is_null($penilaian_utama->$kolom)) {
                  $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
                  if ($penilaian_utama->status == 'Selesai') {
                    $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $nilai_total += $nilai_angka_selesai_utama;
          }

          // $nilai_total *=10;

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update(['nilai_akhir_tahap2' => $nilai_total]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai2.2025.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    } else if ($tahun == '2026') {
      $penilai = DB::select("SELECT id,nama FROM public.users WHERE role='penilai' AND verified is true
				order by
				CASE id
					WHEN 908 THEN 0
					WHEN 1008 THEN 1
					WHEN 911 THEN 2
					WHEN 1007 THEN 3
					WHEN 907 THEN 4
					WHEN 1006 THEN 5
					WHEN 947 THEN 6
					WHEN 1076 THEN 7
					ELSE 8
				END
				");

      switch ($jenis) {
        case 'nilai':
          $tahun = isset($request->tahun) ? $request->tahun : date('Y');

          $user = $data_kec = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('9')
            ->get();

          $data_kel = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('12')
            ->get();

          $user = array_merge($data_kec->toArray(), $data_kel->toArray());

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai2.' . $tahun . '.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $tahun = $request->tahun;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;

            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                $kolom = 'hasil';
                if (!is_null($penilaian_utama->$kolom)) {
                  $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
                  if ($penilaian_utama->status == 'Selesai') {
                    $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('nilai');
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $nilai_total += $nilai_angka_selesai_utama;
          }

          // $nilai_total *=10;

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update(['nilai_akhir_tahap2' => $nilai_total]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai2.' . $tahun . '.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    }
  }

  function nilai_akhir(Request $request)
  {
    $jenis = isset($request->jenis) ? $request->jenis : '-';
    $tahun = $request->tahun;

    if ($tahun == '2024') {
      switch ($jenis) {
        case 'nilai':
          $tahun = isset($request->tahun) ? $request->tahun : date('Y');

          $user = $data_kec = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('9')
            ->get();

          $data_kel = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('12')
            ->get();

          $user = array_merge($data_kec->toArray(), $data_kel->toArray());

          $penilai = DB::select("SELECT nama FROM public.users WHERE role='penilai' order by username ASC");

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai_akhir.2024.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $tahun = $request->tahun;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $penilai = DB::table('users')->where('role', 'penilai')->orderBy('username')->get();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_tahap2 = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();

          $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
          $nama_utama = collect($uraian_utama)->pluck('name');
          $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
          $nama_pendukung = collect($uraian_pendukung)->pluck('name');

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          $nilai_total2 = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;
            $nilai_angka_pendukung = 0;
            $nilai_angka_selesai_pendukung = 0;
            $nilai_angka_tahap2 = 0;
            $nilai_angka_selesai_tahap2 = 0;

            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                for ($i = 0; $i < count($nama_utama); $i++) {
                  $kolom = 'nilai_' . $nama_utama[$i];
                  if (!is_null($penilaian_utama->$kolom)) {
                    $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    if ($penilaian_utama->status == 'Selesai') {
                      $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            if (!empty($nilai_pendukung)) {
              $penilaian_pendukung =  collect($nilai_pendukung)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_pendukung)) {
                for ($i = 0; $i < count($nama_pendukung); $i++) {
                  $kolom = 'nilai_' . $nama_pendukung[$i];
                  if (!is_null($penilaian_pendukung->$kolom)) {
                    $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    if ($penilaian_pendukung->status == 'Selesai') {
                      $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_pendukung)) {
                $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
                if ($penilaian_pendukung->status == 'Selesai') {
                  $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_pendukung = $status_pendukung;
              } else {
                $p->status_pendukung = 'Belum dinilai';
              }
            }

            if (!empty($nilai_tahap2)) {
              $penilaian_tahap2 =  collect($nilai_tahap2)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_tahap2)) {
                $kolom = 'hasil';
                if (!is_null($penilaian_tahap2->$kolom)) {
                  $nilai_angka_tahap2 += collect(json_decode($penilaian_tahap2->$kolom))->sum('nilai');
                  if ($penilaian_tahap2->status == 'Selesai') {
                    $nilai_angka_selesai_tahap2 += collect(json_decode($penilaian_tahap2->$kolom))->sum('nilai');
                  }
                }
              }
              if (!empty($penilaian_tahap2)) {
                $status_tahap2 = $penilaian_tahap2->status . ' (' . $nilai_angka_tahap2 . ' Point)';
                if ($penilaian_tahap2->status == 'Selesai') {
                  $status_tahap2 .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_tahap2->id . '\',\'nilai_kriteria_tahap2\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_tahap2 = $status_tahap2;
              } else {
                $p->status_tahap2 = 'Belum dinilai';
              }
            }

            $p->jumlah_tahap2 = $nilai_angka_tahap2;
            $p->jumlah_tahap2_selesai = $nilai_angka_selesai_tahap2;
            $nilai_total2 += ($nilai_angka_selesai_tahap2 * 10);

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_pendukung = $nilai_angka_pendukung;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $p->jumlah_pendukung_selesai = $nilai_angka_selesai_pendukung;
            $nilai_total += (($nilai_angka_selesai_utama * 4) + ($nilai_angka_selesai_pendukung * 3));
          }

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update([
              'nilai_akhir_tahap1' => $nilai_total,
              'nilai_akhir_tahap2' => $nilai_total2,
              'nilai_akhir' => number_format((((40 / 100) * $nilai_total) + ((60 / 100) * $nilai_total2)), 2, '.', ''),
            ]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai_akhir.2024.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    } else if ($tahun == '2025') {
      $penilai = DB::select("SELECT id,nama FROM public.users WHERE role='penilai' AND verified is true
				order by
				CASE id
					WHEN 907 THEN 0
					WHEN 1006 THEN 1
					WHEN 947 THEN 2
					WHEN 1005 THEN 3
					WHEN 908 THEN 4
					WHEN 1008 THEN 5
					WHEN 911 THEN 6
					WHEN 1007 THEN 7
					ELSE 8
				END
				");

      switch ($jenis) {
        case 'nilai':
          $tahun = isset($request->tahun) ? $request->tahun : date('Y');

          $user = $data_kec = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('9')
            ->get();

          $data_kel = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('12')
            ->get();

          $user = array_merge($data_kec->toArray(), $data_kel->toArray());

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai_akhir.2025.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $tahun = $request->tahun;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_tahap2 = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();

          $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
          $nama_utama = collect($uraian_utama)->pluck('name');
          $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
          $nama_pendukung = collect($uraian_pendukung)->pluck('name');

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          $nilai_total2 = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;
            $nilai_angka_pendukung = 0;
            $nilai_angka_selesai_pendukung = 0;
            $nilai_angka_tahap2 = 0;
            $nilai_angka_selesai_tahap2 = 0;

            // TAHAP 1 UTAMA
            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                for ($i = 0; $i < count($nama_utama); $i++) {
                  $kolom = 'nilai_' . $nama_utama[$i];
                  if (!is_null($penilaian_utama->$kolom)) {
                    $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    if ($penilaian_utama->status == 'Selesai') {
                      $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            // TAHAP 1 PENDUKUNG
            if (!empty($nilai_pendukung)) {
              $penilaian_pendukung =  collect($nilai_pendukung)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_pendukung)) {
                for ($i = 0; $i < count($nama_pendukung); $i++) {
                  $kolom = 'nilai_' . $nama_pendukung[$i];
                  if (!is_null($penilaian_pendukung->$kolom)) {
                    $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    if ($penilaian_pendukung->status == 'Selesai') {
                      $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_pendukung)) {
                $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
                if ($penilaian_pendukung->status == 'Selesai') {
                  $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_pendukung = $status_pendukung;
              } else {
                $p->status_pendukung = 'Belum dinilai';
              }
            }

            // TAHAP 2
            if (!empty($nilai_tahap2)) {
              $penilaian_tahap2 =  collect($nilai_tahap2)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_tahap2)) {
                $kolom = 'hasil';
                if (!is_null($penilaian_tahap2->$kolom)) {
                  $nilai_angka_tahap2 += collect(json_decode($penilaian_tahap2->$kolom))->sum('nilai');
                  if ($penilaian_tahap2->status == 'Selesai') {
                    $nilai_angka_selesai_tahap2 += collect(json_decode($penilaian_tahap2->$kolom))->sum('nilai');
                  }
                }
              }
              if (!empty($penilaian_tahap2)) {
                $status_tahap2 = $penilaian_tahap2->status . ' (' . $nilai_angka_tahap2 . ' Point)';
                if ($penilaian_tahap2->status == 'Selesai') {
                  $status_tahap2 .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_tahap2->id . '\',\'nilai_kriteria_tahap2\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_tahap2 = $status_tahap2;
              } else {
                $p->status_tahap2 = 'Belum dinilai';
              }
            }

            $p->jumlah_tahap2 = $nilai_angka_tahap2;
            $p->jumlah_tahap2_selesai = $nilai_angka_selesai_tahap2;
            $nilai_total2 += ($nilai_angka_selesai_tahap2);

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_pendukung = $nilai_angka_pendukung;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $p->jumlah_pendukung_selesai = $nilai_angka_selesai_pendukung;
            $nilai_total += (($nilai_angka_selesai_utama * 4) + ($nilai_angka_selesai_pendukung * 3));
          }

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update([
              'nilai_akhir_tahap1' => $nilai_total,
              'nilai_akhir_tahap2' => $nilai_total2,
              'nilai_akhir' => number_format((((40 / 100) * $nilai_total) + ((60 / 100) * $nilai_total2)), 2, '.', ''),
            ]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai_akhir.2025.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    } else if ($tahun == '2026') {
      $penilai = DB::select("SELECT id,nama FROM public.users WHERE role='penilai' AND verified is true
				order by
				CASE id
					WHEN 908 THEN 0
					WHEN 1008 THEN 1
					WHEN 911 THEN 2
					WHEN 1007 THEN 3
					WHEN 907 THEN 4
					WHEN 1006 THEN 5
					WHEN 947 THEN 6
					WHEN 1076 THEN 7
					ELSE 8
				END
				");

      switch ($jenis) {
        case 'nilai':
          $tahun = isset($request->tahun) ? $request->tahun : date('Y');

          $user = $data_kec = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kec_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('9')
            ->get();

          $data_kel = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' AND kel_instansi is not null")
            ->orderByRaw("nilai_akhir_tahap1 DESC")
            ->limit('12')
            ->get();

          $user = array_merge($data_kec->toArray(), $data_kel->toArray());

          $data = [
            'user' => $user,
            'tahun' => $tahun,
            'penilai' => $penilai,
          ];

          return view('rekap_api.nilai_akhir.' . $tahun . '.main', $data);
          break;

        case 'nilai-data':
          $user_id = $request->user_id;
          $tahun = $request->tahun;
          $no = $request->i;

          $d = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->first();
          $identitas = DB::table('cutoff_rekap.berkas_identitas')
            ->selectRaw("*,CASE WHEN (user_id is not null AND (surat_pengusulan is null OR sk_penetapan is null OR program is null OR struktur_organisasi is null OR profil_bunda is null OR profil_perkembangan is null)) THEN 'Belum Lengkap' ELSE 'Lengkap' END as status_identitas")
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $nilai_utama = DB::table('cutoff_rekap.nilai_kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_pendukung = DB::table('cutoff_rekap.nilai_kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $nilai_tahap2 = DB::table('cutoff_rekap.nilai_kriteria_tahap2')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->get();
          $kriteria_utama = DB::table('cutoff_rekap.kriteria_utama')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();
          $kriteria_pendukung = DB::table('cutoff_rekap.kriteria_pendukung')
            ->whereRaw("tahun='$tahun' AND user_id='$user_id'")->first();

          $uraian_utama = UraianUtamaLomba::cek_tahun($tahun);
          $nama_utama = collect($uraian_utama)->pluck('name');
          $uraian_pendukung = UraianPendukungLomba::cek_tahun($tahun);
          $nama_pendukung = collect($uraian_pendukung)->pluck('name');

          $d->no = ($no + 1);
          $d->identitas = !empty($identitas) ? $identitas->status_identitas : 'Belum Lengkap';

          if (!empty($kriteria_utama) && !is_null($kriteria_utama->status)) {
            $status_utama = $kriteria_utama->status;
          } else {
            $status_utama = 'Belum diajukan oleh bunda';
          }
          if (!empty($kriteria_pendukung) && !is_null($kriteria_pendukung->status)) {
            $status_pendukung = $kriteria_pendukung->status;
          } else {
            $status_pendukung = 'Belum diajukan oleh bunda';
          }

          $nilai_total = 0;
          $nilai_total2 = 0;
          foreach ($penilai as $p) {
            $nilai_angka_utama = 0;
            $nilai_angka_selesai_utama = 0;
            $nilai_angka_pendukung = 0;
            $nilai_angka_selesai_pendukung = 0;
            $nilai_angka_tahap2 = 0;
            $nilai_angka_selesai_tahap2 = 0;

            // TAHAP 1 UTAMA
            if (!empty($nilai_utama)) {
              $penilaian_utama =  collect($nilai_utama)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_utama)) {
                for ($i = 0; $i < count($nama_utama); $i++) {
                  $kolom = 'nilai_' . $nama_utama[$i];
                  if (!is_null($penilaian_utama->$kolom)) {
                    $nilai_angka_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    if ($penilaian_utama->status == 'Selesai') {
                      $nilai_angka_selesai_utama += collect(json_decode($penilaian_utama->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_utama)) {
                $status_utama = $penilaian_utama->status . ' (' . $nilai_angka_utama . ' Point)';
                if ($penilaian_utama->status == 'Selesai') {
                  $status_utama .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_utama->id . '\',\'nilai_kriteria_utama\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_utama = $status_utama;
              } else {
                $p->status_utama = 'Belum dinilai';
              }
            }

            // TAHAP 1 PENDUKUNG
            if (!empty($nilai_pendukung)) {
              $penilaian_pendukung =  collect($nilai_pendukung)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_pendukung)) {
                for ($i = 0; $i < count($nama_pendukung); $i++) {
                  $kolom = 'nilai_' . $nama_pendukung[$i];
                  if (!is_null($penilaian_pendukung->$kolom)) {
                    $nilai_angka_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    if ($penilaian_pendukung->status == 'Selesai') {
                      $nilai_angka_selesai_pendukung += collect(json_decode($penilaian_pendukung->$kolom))->sum('file');
                    }
                  }
                }
              }
              if (!empty($penilaian_pendukung)) {
                $status_pendukung = $penilaian_pendukung->status . ' (' . $nilai_angka_pendukung . ' Point)';
                if ($penilaian_pendukung->status == 'Selesai') {
                  $status_pendukung .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_pendukung->id . '\',\'nilai_kriteria_pendukung\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_pendukung = $status_pendukung;
              } else {
                $p->status_pendukung = 'Belum dinilai';
              }
            }

            // TAHAP 2
            if (!empty($nilai_tahap2)) {
              $penilaian_tahap2 =  collect($nilai_tahap2)->where('penilai_id', $p->id)->first();
              if (!empty($penilaian_tahap2)) {
                $kolom = 'hasil';
                if (!is_null($penilaian_tahap2->$kolom)) {
                  $nilai_angka_tahap2 += collect(json_decode($penilaian_tahap2->$kolom))->sum('nilai');
                  if ($penilaian_tahap2->status == 'Selesai') {
                    $nilai_angka_selesai_tahap2 += collect(json_decode($penilaian_tahap2->$kolom))->sum('nilai');
                  }
                }
              }
              if (!empty($penilaian_tahap2)) {
                $status_tahap2 = $penilaian_tahap2->status . ' (' . $nilai_angka_tahap2 . ' Point)';
                if ($penilaian_tahap2->status == 'Selesai') {
                  $status_tahap2 .= '&nbsp;<a href="javascript:void(0)" onclick="reset(\'' . $penilaian_tahap2->id . '\',\'nilai_kriteria_tahap2\')"><i class="fa fa-check" style="font-size: 13pt;color: green"></i></a>';
                }
                $p->status_tahap2 = $status_tahap2;
              } else {
                $p->status_tahap2 = 'Belum dinilai';
              }
            }

            $p->jumlah_tahap2 = $nilai_angka_tahap2;
            $p->jumlah_tahap2_selesai = $nilai_angka_selesai_tahap2;
            $nilai_total2 += ($nilai_angka_selesai_tahap2);

            $p->jumlah_utama = $nilai_angka_utama;
            $p->jumlah_pendukung = $nilai_angka_pendukung;
            $p->jumlah_utama_selesai = $nilai_angka_selesai_utama;
            $p->jumlah_pendukung_selesai = $nilai_angka_selesai_pendukung;
            $nilai_total += (($nilai_angka_selesai_utama * 4) + ($nilai_angka_selesai_pendukung * 3));
          }

          $update_nilai = DB::table('cutoff_rekap.peserta_lomba')
            ->whereRaw("tahun='$tahun' and user_id='$user_id'")->update([
              'nilai_akhir_tahap1' => $nilai_total,
              'nilai_akhir_tahap2' => $nilai_total2,
              'nilai_akhir' => number_format((((40 / 100) * $nilai_total) + ((60 / 100) * $nilai_total2)), 2, '.', ''),
            ]);

          $data = [
            'penilai' => $penilai,
            'peserta' => $d,
          ];
          $content = view('rekap_api.nilai_akhir.' . $tahun . '.data', $data)->render();
          return ['content' => $content];
          break;

        default:
          return 'Sorry, you are ugly';
          break;
      }
    }
  }
}
