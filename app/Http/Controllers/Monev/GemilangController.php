<?php

namespace App\Http\Controllers\Monev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Redirect, Session, DB, DataTables;

class GemilangController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $tahun = DB::table('cutoff_rekap.monev_gemilang')->pluck('tahun')->unique()->toArray();
        array_push($tahun, date('Y'));
        $tahun = collect($tahun)->unique()->values()->toArray();
        arsort($tahun);

        $kecamatan = MasterController::data_kecamatan($request);

        $data = [
          'tahun' => $tahun,
          'kecamatan' => $kecamatan['kecamatan'],
          'data_kecamatan' => $kecamatan['semua'],
        ];
        return view('monev_baru.gemilang.main', $data);
        break;

      case 'get_data':
        $tahun_monev = $request->tahun_monev;
        $status_monev = $request->status_monev;

        $tingkat = Auth::getUser()->tingkat;

        $monev = DB::table('cutoff_rekap.monev_gemilang as jp')
          ->where('tahun', $tahun_monev)
          ->get();
        $monev_survey = DB::table('cutoff_rekap.monev_gemilang as jp')
          ->join('cutoff_rekap.monev_gemilang_survey as s', 's.monev_gemilang_id', 'jp.id')
          ->selectRaw("jp.npsn_tk")
          ->where('jp.tahun', $tahun_monev)
          ->get();

        $npsn = collect($monev)->pluck('npsn_tk')->toArray();
        $npsn_survey = collect($monev_survey)->pluck('npsn_tk')->toArray();

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
          ->addColumn('aksi', function ($i) use ($npsn, $npsn_survey, $tahun_monev) {
            $tombol = ' <a href="javascript:void(0)" onclick="monev(\'' . $i->npsn . '\',\'' . $tahun_monev . '\')" class="btn btn-outline-primary">Monev</a>';
            if (in_array($i->npsn, $npsn)) {
              $tombol .= ' <a href="javascript:void(0)" onclick="survey(\'' . $i->npsn . '\',\'' . $tahun_monev . '\')" class="btn btn-outline-warning">Pengisian Survey</a>';
            }
            if (in_array($i->npsn, $npsn_survey)) {
              $tombol .= ' <a href="' . route('monev_baru-gemilang', ['halaman' => 'cetak', 'npsn' => $i->npsn, 'tahun' => $tahun_monev]) . '" target="_blank" class="btn btn-outline-success">Cetak Monev</a>';
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
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $monev = DB::table('cutoff_rekap.monev_gemilang as jp')
          ->where('tahun', $tahun)
          ->where('npsn_tk', $npsn)
          ->first();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("s.alamat,s.npsn,s.nama as nama_lembaga,s.rt,s.rw,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,s.akreditasi")
          ->where('s.npsn', $npsn)->first();

        $data_ks = DB::connection('pgsql_paud')->table('public.pegawai')
          ->selectRaw("nama,telp,hp")
          ->where('npsn', $npsn)
          ->where('jabatan', 2)
          ->where('keterangan', 'Aktif')
          ->first();

        $data = [
          'monev' => $monev,
          'sekolah' => $sekolah,
          'data_ks' => $data_ks,
          'tahun' => $tahun,
        ];

        $content =  view('monev_baru.gemilang.modal_form', $data)->render();
        return ['content' => $content];
        break;

      case 'form_survey':
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $monev = DB::table('cutoff_rekap.monev_gemilang as jp')
          ->leftJoin('cutoff_rekap.monev_gemilang_survey as s', 's.monev_gemilang_id', 'jp.id')
          ->selectRaw("jp.id as id_monev_gemilang,s.*")
          ->where('tahun', $tahun)
          ->where('npsn_tk', $npsn)
          ->first();

        $pertanyaan = $this->pertanyaan();

        $data = [
          'monev' => $monev,
          'tahun' => $tahun,
          'pertanyaan' => $pertanyaan,
        ];

        $content =  view('monev_baru.gemilang.modal_survey', $data)->render();
        return ['content' => $content];
        break;

      case 'cari_sd':
        $cari_sd = $request->cari_sd;
        $sd = DB::connection('db_sd')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->whereRaw("(u.user_enable is true AND s.jenjang in ('SD','MI'))")
          ->where(function ($w) use ($cari_sd) {
            $w->where('s.nama', 'ilike', '%' . $cari_sd . '%')->orWhere('s.npsn', 'ilike', '%' . $cari_sd . '%');;
          })
          ->selectRaw("s.nama,s.npsn,s.alamat")
          ->limit(10)->get();

        $npsn = collect($sd)->pluck('npsn')->toArray();
        $pegawai = DB::connection('db_sd')->table('public.pegawai as p')
          ->whereIn('p.npsn', $npsn)
          ->where('p.keterangan', 'Aktif')
          ->where('jabatan', 2)
          ->selectRaw("p.nama as nama_ks,p.jenis_pegawai,p.npsn as npsn_ks,p.telp,p.hp,p.telp")
          ->get();

        $sd = collect($sd)->map(function ($m) use ($pegawai) {
          $data_ks = $pegawai->where('npsn_ks', $m->npsn)->first();
          return collect($m)->merge($data_ks);
        })->values();

        $data = [
          'sd' => $sd
        ];

        $content = view('monev_baru.gemilang.data_sd', $data)->render();
        return ['content' => $content];
        break;

      case 'simpan':
        // return $request->all();
        $tahun = $request->tahun; //: "69749998",
        $npsn_tk = $request->npsn_tk; //: "69749998",
        $npsn_sd = $request->npsn_sd; //: "20533181",
        $nama_tk = $request->nama_tk; //: "RA HIDAYATULLAH",
        $nama_sd = $request->nama_sd; //: "SDN PANJANGJIWO I \/265",
        $nama_ks_tk = $request->nama_ks_tk; //: "NUR AYNI ZULIATI",
        $nama_ks_sd = $request->nama_ks_sd; //: "KETUT EDY SAPUTRO",
        $telp_tk = $request->telp_tk; //: "085733202753",
        $telp_sd = $request->telp_sd; //: "081331111870",
        $alamat_tk = $request->alamat_tk; //: "Jl. Bratang Wetan 2\/2",
        $alamat_sd = $request->alamat_sd; //: "Jl. Panjangjiwo Besar No 28",
        $jumlah_murid_tk = $request->jumlah_murid_tk; //: "22",
        $jumlah_murid_sd = $request->jumlah_murid_sd; //: "20",
        $jumlah_pegawai_tk = $request->jumlah_pegawai_tk; //: "2",
        $jumlah_pegawai_sd = $request->jumlah_pegawai_sd; //: "2",
        $nama_petugas = $request->nama_petugas; //: "AA",
        $jabatan_petugas = $request->jabatan_petugas; //: "Kasie",
        $telp_petugas = $request->telp_petugas; //: "0989770712903193"
        $tanggal_monev = $request->tanggal_monev; //: "0989770712903193"
        $tanggal_pelaksanaan = $request->tanggal_pelaksanaan; //: "0989770712903193"
        $mulai = $request->mulai; //: "0989770712903193"
        $selesai = $request->selesai; //: "0989770712903193"

        $data_simpan = [
          'npsn_tk' => $npsn_tk,
          'nama_tk' => $nama_tk,
          'nama_ks_tk' => $nama_ks_tk,
          'telp_ks_tk' => $telp_tk,
          'murid_tk' => $jumlah_murid_tk,
          'guru_tk' => $jumlah_pegawai_tk,
          'alamat_tk' => $alamat_tk,
          'npsn_sd' => $npsn_sd,
          'nama_sd' => $nama_sd,
          'nama_ks_sd' => $nama_ks_sd,
          'telp_ks_sd' => $telp_sd,
          'murid_sd' => $jumlah_murid_sd,
          'guru_sd' => $jumlah_pegawai_sd,
          'alamat_sd' => $alamat_sd,
          'tahun' => $tahun,
          'nama_bunda' => $nama_petugas,
          'jabatan' => $jabatan_petugas,
          'hp_bunda' => $telp_petugas,
          'tanggal_monev' => $tanggal_monev,
          'tanggal_pelaksanaan' => $tanggal_pelaksanaan,
          'mulai' => $mulai,
          'selesai' => $selesai,
          'user_id' => Auth::getUser()->id,
        ];

        $simpan = DB::table('cutoff_rekap.monev_gemilang')->updateOrInsert(['npsn_tk' => $npsn_tk, 'tahun' => $tahun], $data_simpan);
        if ($simpan) {
          return ['code' => '200', 'tahun' => $tahun, 'npsn' => $npsn_tk];
        } else {
          return ['code' => '250'];
        }
        break;

      case 'simpan_survey':
        // return $request->all();

        $id_monev_gemilang = $request->id_monev_gemilang;
        $kesan = $request->kesan;
        $saran = $request->saran;
        $isian_1 = json_encode($request->isian_1);
        $isian_2 = json_encode($request->isian_2);
        $isian_3 = json_encode($request->isian_3);
        $isian_4 = json_encode($request->isian_4);
        $isian_5 = json_encode($request->isian_5);
        $isian_6 = json_encode($request->isian_6);
        $isian_7 = json_encode($request->isian_7);

        $data_simpan = [
          'monev_gemilang_id' => $id_monev_gemilang,
          'kelompok_1' => $isian_1,
          'kelompok_2' => $isian_2,
          'kelompok_3' => $isian_3,
          'kelompok_4' => $isian_4,
          'kelompok_5' => $isian_5,
          'kelompok_6' => $isian_6,
          'kelompok_7' => $isian_7,
          'kesan' => $kesan,
          'saran' => $saran,
          'user_id' => Auth::getUser()->id,
          'updated_at' => date('Y-m-d H:i:s'),
        ];

        $simpan = DB::table('cutoff_rekap.monev_gemilang_survey')->updateOrInsert(['monev_gemilang_id' => $id_monev_gemilang], $data_simpan);
        if ($simpan) {
          return ['code' => '200'];
        } else {
          return ['code' => '250'];
        }
        break;
      case 'cetak':
        $npsn = $request->npsn;
        $tahun = $request->tahun;

        $monev = DB::table('cutoff_rekap.monev_gemilang as jp')
          ->leftJoin('cutoff_rekap.monev_gemilang_survey as s', 's.monev_gemilang_id', 'jp.id')
          ->selectRaw("jp.*,jp.id as id_monev_gemilang,s.*")
          ->where('tahun', $tahun)
          ->where('npsn_tk', $npsn)
          ->first();

        $pertanyaan = $this->pertanyaan();

        if (empty($monev)) {
          return '404 - Halaman tidak ditemukan';
        } else {
          $data = [
            'monev' => $monev,
            'pertanyaan' => $pertanyaan,
          ];
          return view('monev_baru.gemilang.cetak', $data);
        }
        break;

      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
  }

  public function pertanyaan()
  {
    $list_pertanyaan = [
      [
        'kode' => 1,
        'kelompok' => 'Penyambutan Murid TK',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah Murid TK disambut di gerbang sekolah>',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah ada salam ramah kepada murid TK?',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah ada gebyar, perayaan atau pentas seni',
          ],
        ],
      ],
      [
        'kode' => 2,
        'kelompok' => 'Kegiatan tour lingkungan Sekolah',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah Ada Kegiatan Tour Lingkungan Sekolah?',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah Murid TK terlihat antusias dan berani bertanya terkait sekolah?',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah tour dipandu oleh Guru SD dengan ramah dan sabar?',
          ],
        ],
      ],
      [
        'kode' => 3,
        'kelompok' => 'Kegiatan Belajar Bersama dengan Guru SD',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah ada Kegiatan Belajar Bersama dengan Guru SD?',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah Guru SD menyajikan materi menyenangkan?',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah Murid TK berpartisipasi aktif dalam pembelajaran?',
          ],
        ],
      ],
      [
        'kode' => 4,
        'kelompok' => 'Dampak untuk murid TK',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah murid TK lebih mengenal lingkungan SD?',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah antusias masuk SD Tahun Depan? (Merasa senang & tidak menangis)',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah murid TK memahami Pelajaran SD kelas awal?',
          ],
        ],
      ],
      [
        'kode' => 5,
        'kelompok' => 'Dampak untuk Guru',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah Guru SD lebih memahami karakteristik murid TK',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah Guru TK lebih mengenal kurikulum SD kelas 1',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah Terjalin komunikasi/ kolaborasi guru TK-SD?',
          ],
          [
            'kode' => 'd',
            'pertanyaan' => 'Apakah ada lembar refleksi Guru?',
          ],
        ],
      ],
      [
        'kode' => 6,
        'kelompok' => 'Dampak untuk Orang Tua',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah Orang tua dilibatkan dalam kegiatan?',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah ada yang mendampingi anak saat kunjungan?',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah orang tua memberikan respon positif terhadap kegiatan?',
          ],
          [
            'kode' => 'd',
            'pertanyaan' => 'Apakah orang tua lebih siap untuk mensekolahkan anak ke SD?',
          ],
        ],
      ],
      [
        'kode' => 7,
        'kelompok' => 'Evaluasi dan Dokumentasi Kegiatan',
        'pertanyaan' => [
          [
            'kode' => 'a',
            'pertanyaan' => 'Apakah ada absensi kegiatan?',
          ],
          [
            'kode' => 'b',
            'pertanyaan' => 'Apakah ada notulensi kegiatan?',
          ],
          [
            'kode' => 'c',
            'pertanyaan' => 'Apakah ada lembar refleksi kegiatan?',
          ],
          [
            'kode' => 'd',
            'pertanyaan' => 'Apakah ada rencana tindak lajut kegiatan?',
          ],
          [
            'kode' => 'e',
            'pertanyaan' => 'Apakah ada publikasi kegiatan di sosial media',
          ],
          [
            'kode' => 'f',
            'pertanyaan' => 'Apakah ada testimoni murid/orang tua/guru?',
          ],
          [
            'kode' => 'g',
            'pertanyaan' => 'Apakah ada laporan ke Dinas Pendidikan?',
          ],
        ],
      ]
    ];
    return $list_pertanyaan;
  }
}
