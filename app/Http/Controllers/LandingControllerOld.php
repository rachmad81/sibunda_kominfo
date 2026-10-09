<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\User;
use App\Models\Ijop;
use App\Models\ProfilPokja;
use App\Models\Kegiatan;
use App\Models\Edu\Jadwal;
use App\Models\Edu\Pendamping;
use Illuminate\Support\Facades\Artisan;
use DB, Auth, Datatables, Waktu, Storage, Redirect;

class LandingController extends Controller
{
  public function halaman(Request $request)
  {
    // if (\Auth::check() && !\Auth::User()->verified) {
    // 	return redirect()->route('common-profil-index')->with('alert-warning','Anda harus memperbarui informasi biodata terlebih dahulu!');
    // }
    $halaman = isset($request->halaman) ? $request->halaman : 'dashboard';
    switch ($halaman) {
      case 'dashboard':
        return $this->dashboard();
        break;

      case 'paud':
        return $this->sekolah($request);
        break;

      case 'kegiatan':
        return $this->kegiatan($request);
        break;

      case 'ijop':
        return $this->ijop($request);
        break;

      case 'get_ijop':
        return $this->get_ijop($request);
        break;

      case 'perbarui_ijop':
        return $this->perbarui_ijop($request);
        break;

      case 'data_bunda':
        return $this->data_bunda($request);
        break;

      case 'get_kelurahan':
        return $this->get_kelurahan($request);
        break;

      case 'city_tour':
        return $this->edu_city($request);
        break;

      case 'get_city_tour':
        return $this->get_data_city_tour($request);
        break;

      case 'buku':
        return $this->buku($request);
        break;

      case 'berkreasi':
        return $this->berkreasi($request);
        break;

      default:
        return $this->dashboard();
        break;
    }
  }

  private function dashboard()
  {

    $bunda = ProfilPokja::whereRaw("id_jabatan in (3,18,33) and deleted_at is null")->orderBy('id_jabatan')->get();

    $sambutan = [
      'judul'  => 'Sambutan Walikota',
      'kata'  => 'Selamat Datang Di SI Bunda Kota Surabaya',
      'nama'  => 'Walikota Surabaya',
      // 'foto'	=> url('/assets/media/photos/cover.jpg')
      'foto'  => url('https://upload.wikimedia.org/wikipedia/commons/b/ba/City_of_Surabaya_Logo.svg')
    ];

    if (Auth::check()) {
      if (Auth::User()->tingkat == 'kota') {
        $data         = User::where('id_jabatan', '3')->first();
        $kata         = ProfilPokja::whereRaw("id_jabatan='3' AND deleted_at is null")->first();
        $sambutan['judul']  = 'Sambutan Bunda Kota Surabaya';

        if ($kata) {
          // $sambutan['foto'] = $kata->path_foto ? route('storage', [base64_encode($kata->path_foto),'1']) : 'https://i.pinimg.com/550x/42/85/cb/4285cb12b952b2c6f2909ce8558f48c9.jpg';
          $sambutan['foto'] = $kata->path_foto ? route('storage', [base64_encode($kata->path_foto), '1']) : 'https://upload.wikimedia.org/wikipedia/commons/b/ba/City_of_Surabaya_Logo.svg';
        }
      } else if (Auth::User()->tingkat == 'kecamatan') {
        $data         = User::whereRaw("role='bunda' AND tingkat='kecamatan' AND id_kecamatan='" . Auth::getUser()->id_kecamatan . "' AND id_jabatan='18'")->first();
        $kata         = ProfilPokja::whereRaw("tingkat='kecamatan' AND id_kecamatan='" . Auth::getUser()->id_kecamatan . "' AND id_jabatan='18' AND deleted_at is null")->first();

        if ($kata) {
          $sambutan['foto'] = $kata->path_foto ? route('storage', [base64_encode($kata->path_foto), '1']) : 'https://upload.wikimedia.org/wikipedia/commons/b/ba/City_of_Surabaya_Logo.svg';
        }
        $sambutan['judul']  = 'Sambutan Bunda Kecamatan';
        $bunda = ProfilPokja::whereRaw("tingkat='kecamatan' AND id_kecamatan='" . Auth::User()->id_kecamatan . "' and deleted_at is null")->orderBy('id_jabatan')->get();
      } else if (Auth::User()->tingkat == 'kelurahan') {
        $data         = User::whereRaw("role='bunda' AND tingkat='kelurahan' AND id_kelurahan='" . Auth::getUser()->id_kelurahan . "' AND id_jabatan='33'")->first();
        $kata         = ProfilPokja::whereRaw("tingkat='kelurahan' AND id_kelurahan='" . Auth::getUser()->id_kelurahan . "' AND id_jabatan='33' AND deleted_at is null")->first();

        if ($kata) {
          $sambutan['foto'] = $kata->path_foto ? route('storage', [base64_encode($kata->path_foto), '1']) : 'https://upload.wikimedia.org/wikipedia/commons/b/ba/City_of_Surabaya_Logo.svg';
        }
        $sambutan['judul']   = 'Sambutan Bunda Kelurahan';
        $bunda = ProfilPokja::whereRaw("tingkat='kelurahan' AND id_kelurahan='" . Auth::User()->id_kelurahan . "' and deleted_at is null")->orderBy('id_jabatan')->get();
      }
    } else {
      $data = User::where('id_jabatan', '1')->first();
    }


    if ($data && $kata) {
      $sambutan['kata']  = $kata->kata_sambutan;
      $sambutan['nama']  = $kata->nama;
    }

    $data = [
      'bunda' => $bunda,
      'menu_active' => 'dashboard',
      'sambutan' => $sambutan,
    ];

    return view('landing.dashboard', $data);
  }

  private function sekolah($request)
  {
    $menu_active = 'paud';
    $addQuery = $cari = '';

    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $kode_jenjang = 'semua';

    if ($request->has('cari')) {
      $cari = $request->cari;
      if ($cari != '') {
        $addQuery .= " AND UPPER(sekolah.nama) like '%" . strtoupper($request->cari) . "%'";
      }
    }

    if ($request->has('kecamatan')) {
      $kode_kecamatan = $request->kecamatan;
      if ($kode_kecamatan != 'semua') {
        $addQuery .= " AND kec_id='" . $kode_kecamatan . "'";
      }
    }

    if ($request->has('kelurahan')) {
      $kode_kelurahan = $request->kelurahan;
      if ($kode_kelurahan != 'semua') {
        $addQuery .= " AND desa='" . $kode_kelurahan . "'";
      }
    }

    if ($request->has('jenjang')) {
      $kode_jenjang = $request->jenjang;
      if ($kode_jenjang != 'semua') {
        $addQuery .= " AND jenjang='" . $kode_jenjang . "'";
      }
    }


    $data = Sekolah::whereHas('master_user', function (Builder $q) {
      $q->whereRaw('user_enable is true');
    })
      ->when(\Auth::check() && \Auth::User()->tingkat == 'kecamatan', function ($q) {
        return $q->whereHas('kecamatan', function (Builder $e) {
          $e->where('kecamatan_id', Auth::User()->id_kecamatan);
        });
      })
      ->when(\Auth::check() && \Auth::User()->tingkat == 'kelurahan', function ($q) {
        return $q->whereHas('kelurahan', function (Builder $e) {
          $e->where('kelurahan_id', Auth::User()->id_kelurahan);
        });
      })
      ->whereRaw("jenjang in ('TK','TPA','KB','SPS','RA')" . $addQuery)
      ->with('kecamatan', 'kelurahan')
      ->orderBy('kec_id')
      ->orderBy('desa')
      ->orderBy('npsn', 'asc')
      ->paginate(9);

    foreach ($data as $key => $value) {
      /*	if ($value->Bangunan && @file_get_contents('https://simbasdispendik.surabaya.go.id/storage/uploads/foto/'.$value->npsn.'/bangunan/'.$value->Bangunan->id_gedung.'/'.$value->Bangunan->foto_depan) !== false) {
				$data[$key]->foto_sekolah = 'https://simbasdispendik.surabaya.go.id/storage/uploads/foto/'.$value->npsn.'/bangunan/'.$value->Bangunan->id_gedung.'/'.$value->Bangunan->foto_depan;
			}
			else{
				$data[$key]->foto_sekolah = 'https://upload.wikimedia.org/wikipedia/commons/d/d1/Image_not_available.png';
			}*/

      if ($value->Bangunan && $value->logo != null) {
        $data[$key]->foto_sekolah = route('storage', [base64_encode('profillama/foto/logo/' . $value->logo), 2]);
      } else {
        $data[$key]->foto_sekolah = 'https://upload.wikimedia.org/wikipedia/commons/d/d1/Image_not_available.png';
      }
    }

    $data->appends(['cari' => $request->cari, 'kecamatan' => $kode_kecamatan, 'kelurahan' => $kode_kelurahan, 'jenjang' => $kode_jenjang]);

    $kecamatan = Kecamatan::orderBy('kecamatan_name', 'ASC')->get();

    return view('landing.daftar-sekolah.index', compact('data', 'menu_active', 'cari', 'kecamatan', 'kode_kecamatan', 'kode_kelurahan', 'kode_jenjang'));
  }

  private function kegiatan(Request $request)
  {
    $menu_active = 'kegiatan';
    $th = $request->th;

    $where = 'id_program is not null';

    if (Auth::check()) {
      if (Auth::getUser()->id_kecamatan != null) {
        $where .= " AND p.id_kecamatan='" . Auth::getUser()->id_kecamatan . "'";
      }
      if (Auth::getUser()->id_kelurahan != null) {
        $where .= " AND p.id_kelurahan='" . Auth::getUser()->id_kelurahan . "'";
      }
      if (Auth::getUser()->tingkat != null) {
        $where .= " AND p.tingkat='" . Auth::getUser()->tingkat . "'";
      }
    }

    $data = Kegiatan::join('public.program as p', 'kegiatan.program_id', 'p.id_program')
      ->selectRaw("*,p.nama_kegiatan as nama_program,kegiatan.nama_kegiatan as kegiatan")
      ->whereRaw("$where")->orderBy('kegiatan.tgl_kegiatan', 'asc', 'p.nama_kegiatan', 'asc')
      ->paginate(9);

    foreach ($data as $key => $value) {
      if ($value->foto != '') {
        $value->url_foto = route('storage', [base64_encode($value->foto), 1]);
      } else {
        $value->url_foto = asset('logo/image-icon.png');
      }
    }

    return view('landing.kegiatan.index', compact('data', 'menu_active'));
  }

  private function ijop(Request $request)
  {
    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $kode_jenjang = 'semua';
    $cari = '';

    if ($request->has('kecamatan')) {
      $kode_kecamatan = $request->kecamatan;
    }

    if ($request->has('kelurahan')) {
      $kode_kelurahan = $request->kelurahan;
    }

    if ($request->has('jenjang')) {
      $kode_jenjang = $request->jenjang;
    }

    $kecamatan = Kecamatan::orderBy('kecamatan_name', 'ASC')->get();
    $jenjang = ['TPA', 'KB', 'TK', 'SPS'];

    $data = [
      'menu_active' => 'ijop',
    ];

    return view('landing.ijop.index', compact('data', 'kecamatan', 'kode_kecamatan', 'kode_kelurahan', 'kode_jenjang', 'jenjang', 'cari'));
  }

  private function get_ijop(Request $request)
  {
    $id_wilayah = '';
    $addQuery = "kelurahan_kode LIKE '%%'";

    if ($request->has('kec')) {
      $kode_kecamatan = $request->kec;
      if ($kode_kecamatan != 'semua') {
        $addQuery .= " AND kecamatan_kode='" . $kode_kecamatan . "'";
      }
    }

    if ($request->has('kel')) {
      $kode_kelurahan = $request->kel;
      if ($kode_kelurahan != 'semua') {
        $addQuery .= " AND kelurahan_kode='" . $kode_kelurahan . "'";
      }
    }

    if ($request->has('jenjang')) {
      $kode_jenjang = $request->jenjang;
      if ($kode_jenjang != 'semua') {
        $addQuery .= " AND jenjang='" . $kode_jenjang . "'";
      }
    }

    if (\Auth::check() && \Auth::User()->tingkat == 'kecamatan') {
      $tmp     = Kecamatan::where('kecamatan_id', \Auth::User()->id_kecamatan)->first();
      $id_wilayah = $tmp->kecamatan_kode;
    } elseif (\Auth::check() && \Auth::User()->tingkat == 'kelurahan') {
      $tmp     = Kelurahan::where('kelurahan_id', \Auth::User()->id_kelurahan)->first();
      $id_wilayah = $tmp->kelurahan_kode;
    }

    $ijops = Ijop::when(\Auth::check() && \Auth::User()->tingkat == 'kecamatan', function ($q) use ($id_wilayah) {
      return $q->where('kecamatan_kode', $id_wilayah);
    })
      ->when(\Auth::check() && \Auth::User()->tingkat == 'kelurahan', function ($q) use ($id_wilayah) {
        return $q->where('kelurahan_kode', $id_wilayah);
      })
      ->whereRaw($addQuery)
      ->orderByRaw("kecamatan_name ASC, kelurahan_name ASC, order_izin_online ASC, order_mati_ijob_2 ASC, order_mati_ijob_1 ASC, order_akan_mati ASC")
      ->with('monitoringIjop');

    return Datatables::of($ijops)
      ->addColumn('status', function ($ijop) {
        return $ijop->getStatus();
      })
      ->addColumn('waktu', function ($ijop) {
        return Waktu::parseFullDate($ijop->selesai_berlaku);
      })
      ->addColumn('hasil_monitoring', function ($ijop) {
        return $ijop->hasilMonitoring();
      })
      ->addColumn('keterangan_monitoring', function ($ijop) {
        return $ijop->hasilMonitoring(true);
      })
      ->setRowClass(function ($ijop) {
        return $ijop->getStatusClass();
      })
      ->rawColumns(['nama_sekolah', 'keterangan_monitoring'])
      ->make();
  }

  function perbarui_ijop(Request $request)
  {
    DB::connection('pgsql')->select('truncate table public.ijops');
    $today = date('Y-m-d');
    $th_lalu = date('Y-m-d', strtotime('-1 years', strtotime($today)));
    $bulan_4 = date('Y-m-d', strtotime('+4 month', strtotime($today)));

    Sekolah::whereHas('master_user', function (Builder $q) {
      return $q->whereRaw('user_enable is true');
    })
      ->where('status', '!=', 'N')
      ->whereIn('jenjang', ['TPA', 'KB', 'TK', 'SPS'])
      ->with('perijinan_npsn_lama', 'perijinan_npsn_baru', 'kecamatan', 'kelurahan')
      ->chunk(100, function ($baris) use ($today, $th_lalu, $bulan_4) {
        $hasil = $baris->map(function ($d) use ($today, $th_lalu, $bulan_4) {
          $data_ijop = $d->getIjop();
          $data_ijop['nama_sekolah'] = $d->nama;
          $data_ijop['npsn'] = $d->npsn;
          $data_ijop['jenjang'] = $d->jenjang;

          $data_ijop['kecamatan_name'] = (!empty($d->kecamatan)) ? $d->kecamatan->kecamatan_name : '';
          $data_ijop['kecamatan_kode'] = (!empty($d->kecamatan)) ? $d->kecamatan->kecamatan_kode : '';
          $data_ijop['kelurahan_name'] = (!empty($d->kelurahan)) ? $d->kelurahan->kelurahan_name : '';
          $data_ijop['kelurahan_kode'] = (!empty($d->kelurahan)) ? $d->kelurahan->kelurahan_kode : '';

          // IJOP TERDAFTAR/TIDAK (1=terdaftar,0=tidak terdaftar)
          $data_ijop['order_izin_online'] = ($data_ijop['izin_online'] == true) ? 1 : 0;
          $data_ijop['str_izin_online'] = ($data_ijop['izin_online'] == true) ? "Terdaftar" : "Tidak Terdaftar";

          // MATI IJOP > 1th
          $data_ijop['order_mati_ijob_2'] = 1;
          $data_ijop['str_mati_ijob_2'] = '';
          if (($data_ijop['selesai_berlaku'] == null) or ($data_ijop['selesai_berlaku'] != null && ($data_ijop['selesai_berlaku'] < $th_lalu))) {
            $data_ijop['order_mati_ijob_2'] = 0;
            $data_ijop['str_mati_ijob_2'] = 'Mati > 1 Th';
          }

          // MATI IJOP < 1th
          $data_ijop['order_mati_ijob_1'] = 1;
          $data_ijop['str_mati_ijob_1'] = '';
          if (($data_ijop['selesai_berlaku'] == null) or ($data_ijop['selesai_berlaku'] != null && ($data_ijop['selesai_berlaku'] >= $th_lalu && $data_ijop['selesai_berlaku'] < $today))) {
            $data_ijop['order_mati_ijob_1'] = 0;
            $data_ijop['str_mati_ijob_1'] = 'Mati < 1 Th';
          }

          // AKAN MATI 1-4 Bulan
          $data_ijop['order_akan_mati'] = 1;
          $data_ijop['str_akan_mati'] = '';
          if (($data_ijop['selesai_berlaku'] == null) or ($data_ijop['selesai_berlaku'] != null && ($data_ijop['selesai_berlaku'] >= $today && $data_ijop['selesai_berlaku'] < $bulan_4))) {
            $data_ijop['order_akan_mati'] = 0;
            $data_ijop['str_akan_mati'] = 'Akan Mati < 4 Bulan Kedepan';
          }
          return $data_ijop;
        })->toArray();

        DB::connection('pgsql')->table('public.ijops')->insert($hasil);
      });

    return ['status' => '200', 'message' => 'Data sudah diperbarui'];
  }

  function data_bunda(Request $request)
  {
    $id = $request->id;

    $bunda = ProfilPokja::join('public.jabatan_pokja as jp', 'jp.id', 'profil_pokja.id_jabatan')
      ->selectRaw("profil_pokja.*,jp.nama_jabatan")->find($id);

    if ($bunda->tingkat == 'kecamatan') {
      $kecamatan = Kecamatan::where('kecamatan_id', $bunda->id_kecamatan)->first();

      $nama_kecamatan = !empty($kecamatan) ? ucwords(strtolower($kecamatan->kecamatan_dispenduk)) : '';

      $tingkat = !empty($kecamatan) ? '(Kec. ' . $nama_kecamatan . ')' : '';
    } else if ($bunda->tingkat == 'kelurahan') {
      $kelurahan = Kelurahan::where('kelurahan_id', $bunda->id_kelurahan)->first();
      $kecamatan = Kecamatan::where('kecamatan_kode', $kelurahan->kecamatan_kode)->first();

      $nama_kelurahan = !empty($kelurahan) ? ucwords(strtolower($kelurahan->kelurahan_dispenduk)) : '';
      $nama_kecamatan = !empty($kecamatan) ? ucwords(strtolower($kecamatan->kecamatan_dispenduk)) : '';

      $tingkat = !empty($kelurahan) ? '(Kel. ' . $nama_kelurahan . ' (Kec. ' . $nama_kecamatan . '))' : '';
    } else {
      $tingkat = '';
    }

    $bunda->nama_bunda = $bunda->nama;
    $bunda->jabatan = $tingkat;

    $content = view('landing.data_bunda', ['bunda' => $bunda])->render();

    return ['content' => $content];
  }

  function get_kelurahan(Request $request)
  {
    $kode_kecamatan = $request->kode_kecamatan;

    $kelurahan = Kelurahan::where('kecamatan_kode', $kode_kecamatan)->get();

    return $kelurahan;
  }

  private function edu_city($request)
  {
    $menu_active = 'edu';

    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $kode_jenjang = 'semua';
    $tahun = date('Y');
    $tahun_edu = Jadwal::on('db_simpaud')->selectRaw("EXTRACT(YEAR from tanggal) as tahun")->groupBy('tahun')->get();

    $data_tahun = $tahun_edu->pluck('tahun')->toArray();
    array_push($data_tahun, $tahun);
    array_unique($data_tahun);
    sort($data_tahun, 0);

    $kecamatan = Kecamatan::orderBy('kecamatan_name', 'ASC')->get();
    $data = [
      'cari' => $request->cari,
      'kode_kecamatan' => $kode_kecamatan,
      'kode_kelurahan' => $kode_kelurahan,
      'kode_jenjang' => $kode_jenjang,
      'kecamatan' => $kecamatan,
      'data_tahun' => $data_tahun,
      'tahun' => $tahun,
    ];

    return view('landing.edu.index', $data);
  }

  function get_data_city_tour(Request $request)
  {
    $menu_active = 'edu';
    $addQuery = $cari = '';

    $kode_kecamatan = 'semua';
    $kode_kelurahan = 'semua';
    $kode_jenjang = 'semua';
    $tahun = date('Y');

    if ($request->has('tahun')) {
      $tahun = $request->tahun;
    }

    if ($request->has('cari')) {
      $cari = $request->cari;
      if ($cari != '') {
        $addQuery .= " AND UPPER(sekolah.nama) like '%" . strtoupper($request->cari) . "%'";
      }
    }

    if ($request->has('kec')) {
      $kode_kecamatan = $request->kec;
      if ($kode_kecamatan != 'semua') {
        $addQuery .= " AND kec_id='" . $kode_kecamatan . "'";
      }
    }

    if ($request->has('kel')) {
      $kode_kelurahan = $request->kel;
      if ($kode_kelurahan != 'semua') {
        $addQuery .= " AND desa='" . $kode_kelurahan . "'";
      }
    }

    if ($request->has('jenjang')) {
      $kode_jenjang = $request->jenjang;
      if ($kode_jenjang != 'semua') {
        $addQuery .= " AND jenjang='" . $kode_jenjang . "'";
      }
    }


    $sekolah = Sekolah::whereHas('master_user', function (Builder $q) {
      $q->whereRaw('user_enable is true');
    })
      ->when(\Auth::check() && \Auth::User()->tingkat == 'kecamatan', function ($q) {
        return $q->whereHas('kecamatan', function (Builder $e) {
          $e->where('kecamatan_id', Auth::User()->id_kecamatan);
        });
      })
      ->when(\Auth::check() && \Auth::User()->tingkat == 'kelurahan', function ($q) {
        return $q->whereHas('kelurahan', function (Builder $e) {
          $e->where('kelurahan_id', Auth::User()->id_kelurahan);
        });
      })
      ->whereRaw("jenjang in ('TK','TPA','KB','SPS')" . $addQuery)
      ->with('kecamatan', 'kelurahan')
      ->orderBy('kec_id')
      ->orderBy('desa')
      ->orderBy('npsn', 'asc')->get();

    $npsn = $sekolah->pluck('npsn')->toArray();
    $npsn = array_unique($npsn);

    if ($tahun != 'semua') {
      $where_tahun = "EXTRACT(YEAR FROM tanggal) = '" . $tahun . "'";
    } else {
      $where_tahun = "";
    }

    $bkb = Jadwal::on('db_simpaud')
      ->join('public.edu_city_tour_tempats as tempat', 'tempat.id', 'edu_city_tour_jadwals.tempat_id')
      ->selectRaw("edu_city_tour_jadwals.*,tempat.nama as nama_tempat,tempat.alamat as alamat_tempat,tempat.htm as htm_tempat")
      ->whereIn('npsn', $npsn)
      ->whereRaw($where_tahun)->get();

    if ($bkb->count() == 0) {
      return ['data' => []];
    }

    return DataTables()->of($bkb)
      ->addIndexColumn()
      ->addColumn('nama_sekolah', function ($akun) use ($sekolah) {
        $data_sekolah = $sekolah->where('npsn', $akun->npsn)->first();
        $nama_sekolah = $data_sekolah->nama;
        $akun->nama_kecamatan = $data_sekolah->kecamatan->kecamatan_name;
        $akun->nama_kelurahan = $data_sekolah->kelurahan->kelurahan_name;
        return $nama_sekolah;
      })
      ->addColumn('nama_kecamatan', function ($akun) {
        return $akun->nama_kecamatan;
      })
      ->addColumn('nama_kelurahan', function ($akun) {
        return $akun->nama_kelurahan;
      })
      ->addColumn('pendampings', function ($akun) {
        $pendamping = Pendamping::on('db_simpaud')->where('edu_city_tour_jadwal_id', $akun->id)->orderBy('id', 'ASC')->get();
        $namanya = '';
        foreach ($pendamping as $p) {
          $namanya .= $p->nama_pegawai . ' <b>(' . $p->jabatan . ')</b><br><br>';
        }

        return $namanya;
      })
      ->addColumn('aksi', function ($akun) use ($tahun) {
        $button = '';
        $button .= '<a style="margin-left: 5px" href="' . route('bunda-edu-kehadiran', ['id' => $akun->id]) . '" target="_blank" class="btn btn-sm btn-primary">Kehadiran</a>';
        return $button;
      })
      ->rawColumns(['aksi', 'pendampings'])
      ->make();
  }

  private function buku(Request $request)
  {
    $buku = DB::table('buku')->orderBy('created_at', 'DESC')->paginate(9);

    $data = [
      'menu_active' => 'buku',
      'buku' => $buku,
    ];

    return view('landing.buku.index', $data);
  }

  private function berkreasi(Request $request)
  {

    $data = [
      'menu_active' => 'berkreasi',
    ];

    return view('landing.berkreasi.index', $data);
  }

  function login()
  {
    return view('auth.login');
  }

  function home()
  {
    return Redirect::to('/landing/home');
  }

  public function clear()
  {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return response()->json([
      'status' => 'ok'
    ]);
  }
}
