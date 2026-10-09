<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JabatanPokja;
use App\Models\Paudhi\InstrumenPengajuan;
use App\Models\Paudhi\Instrumen;
use App\Models\Paudhi\KategoriInstrumen;
use App\Models\Paudhi\PengajuanPaudHI;
use App\Models\Paudhi\Catatan;
use App\Models\Paudhi\PengajuanCatatan;
use App\Http\Libraries\GetData;
use DataTables, DB, Session, Auth;
use App\Tools\Bantuan;
use Carbon\Carbon;

class PaudController extends Controller
{
  function __construct()
  {
    date_default_timezone_set('Asia/Jakarta');
  }

  function index()
  {
    $th = [];
    for ($i = 2023; $i <= date('Y'); $i++) {
      $baris['th'] = $i;
      array_push($th, $baris);
    }
    // $th = PengajuanPaudHI::selectRaw("tahun_pengajuan as th")->whereNotNull('tanggal')->groupBy('tahun_pengajuan')->get();
    $kecamatan = [];
    $kelurahan = [];
    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $jenjang = 'semua';

    $kecamatan = DB::connection('pgsql_paud')->table('public.kecamatan')->orderBy('kecamatan_dispenduk', 'ASC')->get();
    if (Auth::getUser()->tingkat == 'kelurahan') {
      $gk = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kelurahan_id', Auth::getUser()->id_kelurahan)->first();
      $kelurahan = GetData::get_kelurahan($gk->kecamatan_kode);
      $kode_kelurahan = $gk->kelurahan_kode;
      $kode_kecamatan = $gk->kecamatan_kode;
    } else {
      if (Auth::getUser()->tingkat == 'kecamatan') {
        $gk = DB::connection('pgsql_paud')->table('public.kecamatan')->where('kecamatan_id', Auth::getUser()->id_kecamatan)->first();
        $kode_kecamatan = $gk->kecamatan_kode;
      }
    }

    $data = [
      'thn' => $th,
      'tahun' => date('Y'),
      'kecamatan' => $kecamatan,
      'kelurahan' => $kelurahan,
      'kode_kecamatan' => $kode_kecamatan,
      'kode_kelurahan' => $kode_kelurahan,
      'jenjang' => $jenjang,
    ];

    return view('bunda.paud_hi.index', $data);
  }

  function get_data(Request $request)
  {
    $tahun = $request->tahun;
    $kec = $request->kec;
    $kel = $request->kel;
    $jenjang = $request->jenjang;

    $where_lokasi = "";
    if ($kec != 'semua') {
      $where_lokasi .= " AND s.kec_id='$kec'";
    }
    if ($kel != 'semua') {
      $where_lokasi .= " AND s.desa='$kel'";
    }
    if ($jenjang != 'semua') {
      $where_lokasi .= " AND s.jenjang='$jenjang'";
    }

    $pengajuan = PengajuanPaudHI::join('public.sekolah as s', 's.npsn', 'pengajuan_paud_hi.npsn')
      ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
      ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
      ->join('public.master_user as u', 'u.npsn', 's.npsn')
      ->selectRaw("s.npsn,s.nama,s.jenjang,s.alamat,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,pengajuan_paud_hi.id,pengajuan_paud_hi.created_at,pengajuan_paud_hi.status_pengajuan")
      ->whereRaw("u.user_enable is true AND s.jenjang IN ('TK','KB','TPA','SPS') AND pengajuan_paud_hi.tahun_pengajuan='$tahun' $where_lokasi")->orderByRaw("status_pengajuan='Diajukan' ASC,pengajuan_paud_hi.created_at ASC")
      ->get();

    $id_pengajuan = $pengajuan->pluck('id');
    $npsn = $pengajuan->pluck('npsn');

    $score = InstrumenPengajuan::join('paud_hi.instrumen as i', 'i.id', 'instrumen_pengajuan.instrumen_id')->join('paud_hi.pengajuan_paud_hi as h', 'h.id', 'instrumen_pengajuan.pengajuan_paud_hi_id')->selectRaw("h.id,COUNT(h.id) as jumlah")->whereIn('h.id', $id_pengajuan)->whereRaw("instrumen_pengajuan.hasil_pemantauan is true")->groupByRaw("h.id")->get();
    $jumlah_pegawai = DB::connection('pgsql_paud')->table('public.pegawai')->selectRaw("npsn,COUNT(npsn) as jumlah")->whereIn('npsn', $npsn)->whereRaw("keterangan='Aktif'")->groupBy('npsn')->get();
    $jumlah_siswa = DB::connection('pgsql_paud')->table('public.siswa')->selectRaw("npsn,COUNT(npsn) as jumlah")->whereIn('npsn', $npsn)->whereRaw("status_siswa='Aktif' AND alumni is not true")->groupBy('npsn')->get();

    return DataTables()->of($pengajuan)
      ->addIndexColumn()
      ->addColumn('aksi', function ($akun) use ($tahun) {
        $button = '';
        if ($akun->status_pengajuan != null) {
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_hi(\'' . $akun->id . '\',\'' . $tahun . '\',\'profil\')" class="btn btn-sm btn-primary">Profil HI</a>';
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_hi(\'' . $akun->id . '\',\'' . $tahun . '\',\'indikator\')" class="btn btn-sm btn-primary">Indikator HI</a>';
          $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_hi(\'' . $akun->id . '\',\'' . $tahun . '\',\'instrumen\')" class="btn btn-sm btn-primary">Instrumen HI</a>';
          if (in_array($akun->status_pengajuan, ['Diajukan'])) {
            $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="setujui(\'' . $akun->id . '\')" class="btn btn-sm btn-success">Setujui</a>';
            $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="modal_hi(\'' . $akun->id . '\',\'' . $tahun . '\',\'tolak\')" class="btn btn-sm btn-danger">Tolak</a>';
          } else {
            $button .= '<a style="margin-left: 5px" href="javascript:void(0)" onclick="riwayat_catatan_pengajuan(\'' . $akun->id . '\')" class="btn btn-sm btn-warning">Riwayat Catatan</a>';
          }
        } else {
          $button .= 'Sekolah belum melakukan pengajuan';
        }
        return $button;
      })
      ->addColumn('score', function ($j) use ($score) {
        $jumlah = $score->where('id', $j->id)->first();
        if (!empty($jumlah)) {
          $nilai = $jumlah->jumlah;
        } else {
          $nilai = 0;
        }

        return  $nilai;
      })
      ->addColumn('jumlah_gtk', function ($j) use ($jumlah_pegawai) {
        $jumlah = $jumlah_pegawai->where('npsn', $j->npsn)->first();
        if (!empty($jumlah)) {
          $nilai = $jumlah->jumlah;
        } else {
          $nilai = 0;
        }

        return  $nilai;
      })
      ->addColumn('jumlah_pd', function ($j) use ($jumlah_siswa) {
        $jumlah = $jumlah_siswa->where('npsn', $j->npsn)->first();
        if (!empty($jumlah)) {
          $nilai = $jumlah->jumlah;
        } else {
          $nilai = 0;
        }

        return  $nilai;
      })
      ->addColumn('cetak', function ($j) {
        $button = '<a href="' . route('bunda-paud_hi-cetak', ['id' => $j->id]) . '" target="_blank" class="btn btn-sm btn-warning">Cetak</a>';
        return $button;
      })
      ->editColumn('status_pengajuan', function ($akun) {
        if ($akun->status_pengajuan == null) {
          $return = '<a href="javascript:void(0)" class="alert alert-warning"></a>';
        } else if ($akun->status_pengajuan == 'Diajukan') {
          $return = '<a href="javascript:void(0)" class="alert alert-primary">' . $akun->status_pengajuan . '</a>';
        } else if ($akun->status_pengajuan == 'Tolak') {
          $return = '<a href="javascript:void(0)" class="alert alert-danger">' . $akun->status_pengajuan . '</a>';
        } else if ($akun->status_pengajuan == 'Setujui') {
          $return = '<a href="javascript:void(0)" class="alert alert-success">' . $akun->status_pengajuan . '</a>';
        }

        return $return;
      })
      ->editColumn('created_at', function ($akun) {
        return date('Y-m-d H:i:s', strtotime($akun->created_at));
      })
      ->rawColumns(['aksi', 'status_pengajuan', 'cetak'])
      ->make(true);
  }

  function modal(Request $request)
  {
    $id = $request->id;
    $tahun = $request->tahun;
    $kategori = $request->kategori;

    if ($kategori == 'profil') {
      $content = $this->profil_hi($request);
    } else if ($kategori == 'indikator') {
      $content = $this->indikator_hi($request);
    } elseif ($kategori == 'instrumen') {
      $content = $this->instrumen_hi($request);
    } elseif ($kategori == 'tolak') {
      $content = $this->modal_tolak($request);
    }

    return $content;
  }

  function profil_hi(Request $request)
  {
    $id = $request->id;
    $tahun = $request->tahun;
    $kategori = $request->kategori;

    $pengajuan = PengajuanPaudHI::where('id', $id)->first();

    $data = [
      'pengajuan' => $pengajuan,
    ];

    $content = view('bunda.paud_hi.profil', $data)->render();

    return ['content' => $content];
  }

  function indikator_hi(Request $request)
  {
    $id = $request->id;
    $tahun = $request->tahun;
    $kategori = $request->kategori;

    $pengajuan = PengajuanPaudHI::where('id', $id)->first();

    $data = [
      'pengajuan' => $pengajuan,
    ];

    $content = view('bunda.paud_hi.indikator', $data)->render();

    return ['content' => $content];
  }

  function instrumen_hi(Request $request)
  {
    $data = $this->get_instrumen_hi($request);

    $content = view('bunda.paud_hi.instrumen', $data)->render();

    return ['content' => $content];
  }

  function get_instrumen_hi(Request $request)
  {
    $id = $request->id;
    $tahun = $request->tahun;
    if ($tahun == 2023) {
      $tahun == 2022;
    }
    $kategori = $request->kategori;

    $pengajuan = PengajuanPaudHI::where('id', $id)
      ->with(['InstrumenPengajuan' => function ($q) {
        $q->with(['Instrumen' => function ($q) {
          $q->with(['Kategori' => function ($q) {
            $q->with('Parent');
          }])
            ->with(['Parent' => function ($q) {
              $q->with('Parent');
            }]);
        }])->orderBy('instrumen_id', 'ASC');
      }])
      ->first();

    $categories = KategoriInstrumen::where('tahun', in_array($pengajuan->tahun_pengajuan, [2023, 2024]) ? 2022 : $pengajuan->tahun_pengajuan)
      ->doesnthave('Parent')
      ->with(['Children' => function ($q) {
        $q->orderBy('order', 'ASC')
          ->with(['Instrumen' => function ($q) {
            $q->orderBy('order', 'ASC')
              ->where('parent_id', '=', null)
              ->with('Parent')
              ->with(['Children' => function ($q) {
                $q->orderBy('order', 'ASC')
                  ->with(['Children' => function ($q) {
                    $q->orderBy('order', 'ASC');
                  }]);
              }]);
          }]);
      }])
      ->with(['Instrumen' => function ($q) {
        $q->orderBy('order', 'ASC')
          ->where('parent_id', '=', null)
          ->with(['Children' => function ($q) {
            $q->orderBy('order', 'ASC')
              ->with(['Children' => function ($q) {
                $q->orderBy('order', 'ASC');
              }]);
          }]);
      }])
      ->orderBy('order', 'ASC')
      ->get();

    $listOfCategories = [];
    foreach ($categories as $categoryKey => $category) {
      if (!$category->Children->isEmpty()) {
        foreach ($category->Children as $categoryChildKey => $categoryChild) {
          array_push($listOfCategories, $categoryChild);
        }
      } else {
        array_push($listOfCategories, $category);
      }

      foreach ($listOfCategories as $key => $category) {
        foreach ($category->Instrumen as $instrumenKey => $instrumen) {
          if ($instrumen->is_fillable == true) {
            $finding = $pengajuan->InstrumenPengajuan->where('instrumen_id', $instrumen->id)->where('is_dynamic_child', false)->first();
            if ($finding) {
              $hasil = $finding->hasil_pemantauan;
              $file = $finding->file;
              $instrumen->hasil_pemantauan = $hasil;
              $instrumen->file = $file;
              $instrumen->komentar = $finding->komentar;
              $instrumen->komentator = $finding->komentator;
            }
          }
          if ($instrumen->has_dynamic_children == true) {
            $findings = $pengajuan->InstrumenPengajuan->where('instrumen_id', $instrumen->id)->where('is_dynamic_child', true)->sortBy('id');
            $instrumen->dynamic_children = $findings;
          }
          if (!$instrumen->Children->isEmpty()) {
            foreach ($instrumen->Children as $instrumenChildKey => $instrumenChild) {
              if ($instrumenChild->is_fillable == true) {
                $finding = $pengajuan->InstrumenPengajuan->where('instrumen_id', $instrumenChild->id)->where('is_dynamic_child', false)->first();
                if ($finding) {
                  $hasil = $finding->hasil_pemantauan;
                  $file = $finding->file;
                  $instrumenChild->hasil_pemantauan = $hasil;
                  $instrumenChild->file = $file;
                  $instrumenChild->komentar = $finding->komentar;
                  $instrumenChild->komentator = $finding->komentator;
                }
              }
              if ($instrumenChild->has_dynamic_children == true) {
                $findings = $pengajuan->InstrumenPengajuan->where('instrumen_id', $instrumenChild->id)->where('is_dynamic_child', true)->sortBy('id');
                $instrumenChild->dynamic_children = $findings;
              }
              if (!$instrumenChild->Children->isEmpty()) {
                foreach ($instrumenChild->Children as $instrumenGrandChildKey => $instrumenGrandChild) {
                  if ($instrumenGrandChild->is_fillable == true) {
                    $finding = $pengajuan->InstrumenPengajuan->where('instrumen_id', $instrumenGrandChild->id)->where('is_dynamic_child', false)->first();
                    if ($finding) {
                      $hasil = $finding->hasil_pemantauan;
                      $file = $finding->file;
                      $instrumenGrandChild->hasil_pemantauan = $hasil;
                      $instrumenGrandChild->file = $file;
                      $instrumenGrandChild->komentar = $finding->komentar;
                      $instrumenGrandChild->komentator = $finding->komentator;
                    }
                  }
                  if ($instrumenGrandChild->has_dynamic_children == true) {
                    $findings = $pengajuan->InstrumenPengajuan->where('instrumen_id', $instrumenGrandChild->id)->where('is_dynamic_child', true)->sortBy('id');
                    $instrumenGrandChild->dynamic_children = $findings;
                  }
                }
              }
            }
          }
        }
      }
    }

    $score = InstrumenPengajuan::join('paud_hi.instrumen as i', 'i.id', 'instrumen_pengajuan.instrumen_id')->join('paud_hi.pengajuan_paud_hi as h', 'h.id', 'instrumen_pengajuan.pengajuan_paud_hi_id')->whereRaw("h.id='$id' AND instrumen_pengajuan.hasil_pemantauan is true")->count();

    $data = [
      'categories' => $categories,
      'pengajuan' => $pengajuan,
      'hasil_iya' => $score,
    ];

    return $data;
  }

  function simpan(Request $request)
  {
    $id = $request->id;
    $id_pengajuan = $request->id_pengajuan;
    $kategori = $request->kategori;
    $komentar = $request->komentar;

    if ($kategori == 'parent') {
      $where = "instrumen_id='$id' AND pengajuan_paud_hi_id='$id_pengajuan'";
    } else {
      $where = "id='$id' AND pengajuan_paud_hi_id='$id_pengajuan'";
    }

    $instrumen_pengajuan = InstrumenPengajuan::whereRaw("$where")->first();
    if (!empty($instrumen_pengajuan)) {
      $user = User::find(Auth::id());
      $komentator = (!empty($user)) ? Auth::getUser()->getNamaProfil() : '';

      $instrumen_pengajuan->komentar = $komentar;
      $instrumen_pengajuan->komentator = $komentator;
      $instrumen_pengajuan->save();
      if ($instrumen_pengajuan) {
        $catatan = new Catatan;
        $catatan->instrumen_pengajuan_id = $instrumen_pengajuan->id;
        $catatan->komentar = $komentar;
        $catatan->komentator = $komentator;
        $catatan->created_at = date('Y-m-d H:i:s');
        $catatan->save();
        $return = ['code' => '200', 'msg' => 'Berhasil disimpan', 'komentar' => $komentar, 'komentator' => $komentator];
      } else {
        $return = ['code' => '250', 'msg' => 'Gagal dikomentari'];
      }
    } else {
      $return = ['code' => '250', 'msg' => 'Data belum diisi oleh sekolah'];
    }
    return $return;
  }

  function riwayat(Request $request)
  {
    $id = $request->id;
    $id_pengajuan = $request->id_pengajuan;
    $kategori = $request->kategori;
    $komentar = $request->komentar;

    if ($kategori == 'parent') {
      $where = "instrumen_id='$id' AND pengajuan_paud_hi_id='$id_pengajuan'";
    } else {
      $where = "id='$id' AND pengajuan_paud_hi_id='$id_pengajuan'";
    }

    $instrumen_pengajuan = InstrumenPengajuan::leftjoin('paud_hi.instrumen as i', 'i.id', 'instrumen_pengajuan.instrumen_id')
      ->leftjoin('paud_hi.kategori_instrumen as k', 'k.id', 'i.kategori_id')
      ->selectRaw("instrumen_pengajuan.id,i.nama,k.nama as nama_kategori")
      ->whereRaw("$where")->first();
    if (!empty($instrumen_pengajuan)) {
      $catatan = Catatan::where('instrumen_pengajuan_id', $instrumen_pengajuan->id)->orderBy('created_at', 'DESC')->get();

      $data = [
        'instrumen' => $instrumen_pengajuan,
        'catatan' => $catatan,
      ];
      $content = view('bunda.paud_hi.riwayat', $data)->render();;
      $return = ['code' => '200', 'content' => $content];
    } else {
      $return = ['code' => '250', 'content' => '', 'msg' => 'Data belum diisi oleh sekolah'];
    }
    return $return;
  }

  function modal_tolak(Request $request)
  {
    $id = $request->id;
    $tahun = $request->tahun;
    $kategori = $request->kategori;

    $pengajuan = PengajuanPaudHI::where('id', $id)->first();

    $data = [
      'pengajuan' => $pengajuan,
    ];

    $content = view('bunda.paud_hi.tolak', $data)->render();

    return ['content' => $content];
  }

  function tolak(Request $request)
  {
    // return $request->all();
    $alasan = $request->alasan;
    $id = $request->id;

    $pengajuan = PengajuanPaudHI::find($id);
    $pengajuan->catatan_sibunda = $alasan;
    $pengajuan->status_pengajuan = 'Tolak';
    $pengajuan->save();

    if ($pengajuan) {
      $r_catatan_pengajuan = new PengajuanCatatan;
      $r_catatan_pengajuan->catatan = $alasan;
      $r_catatan_pengajuan->komentator = Auth::getUser()->getNamaProfil();
      $r_catatan_pengajuan->status_perubahan = 'Tolak';
      $r_catatan_pengajuan->created_at = date('Y-m-d H:i:s');
      $r_catatan_pengajuan->pengajuan_id = $pengajuan->id;
      $r_catatan_pengajuan->save();

      if ($r_catatan_pengajuan) {
        return ['code' => '200', 'msg' => 'Berhasil disimpan'];
      }
    }
    return ['code' => '200', 'msg' => 'Gagal disimpan'];
  }

  function setujui(Request $request)
  {
    // return $request->all();
    $id = $request->id;

    $pengajuan = PengajuanPaudHI::find($id);
    $pengajuan->status_pengajuan = 'Setujui';
    $pengajuan->tanggal_disetujui = date('Y-m-d H:i:s');
    $pengajuan->save();

    if ($pengajuan) {
      $r_catatan_pengajuan = new PengajuanCatatan;
      $r_catatan_pengajuan->catatan = 'Setujui';
      $r_catatan_pengajuan->komentator = Auth::getUser()->getNamaProfil();
      $r_catatan_pengajuan->status_perubahan = 'Setujui';
      $r_catatan_pengajuan->created_at = date('Y-m-d H:i:s');
      $r_catatan_pengajuan->pengajuan_id = $pengajuan->id;
      $r_catatan_pengajuan->save();

      if ($r_catatan_pengajuan) {
        return ['code' => '200', 'msg' => 'Berhasil disimpan'];
      }
    }
    return ['code' => '200', 'msg' => 'Gagal disimpan'];
  }

  function riwayat_catatan_pengajuan(Request $request)
  {
    $id = $request->id;

    $pengajuan = PengajuanPaudHI::find($id);
    $catatan_pengajuan = PengajuanCatatan::where('pengajuan_id', $id)->orderBy('created_at', 'DESC')->get();

    $data = [
      'pengajuan' => $pengajuan,
      'catatan_pengajuan' => $catatan_pengajuan,
    ];

    $content = view('bunda.paud_hi.riwayat_pengajuan', $data)->render();

    return ['content' => $content];
  }

  function get_kelurahan(Request $request)
  {
    $id = $request->id;
    $kelurahan = GetData::get_kelurahan($id);

    return $kelurahan;
  }

  function cetak(Request $request)
  {
    $id = $request->id;

    $profil = PengajuanPaudHI::where('id', $id)->first();
    $instrumen = $this->get_instrumen_hi($request);

    $data = [
      'profil' => $profil,
    ];

    $data = array_merge($data, $instrumen);

    return view('bunda.paud_hi.cetak', $data);
  }
}
