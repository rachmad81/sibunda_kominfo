<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProfilSekolah\Sekolah;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Redirect, Auth, DB, Storage, Datatables, Waktu, Hash;

class LandingController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = isset($request->halaman) ? $request->halaman : 'dashboard';
    switch ($halaman) {
      case 'home':
        $user = DB::table('public.profil_pokja as p')
          ->join('public.jabatan_pokja as j', 'j.id', 'p.id_jabatan')
          ->leftjoin('public.kecamatan as kec', 'kec.kecamatan_id', 'p.id_kecamatan')
          ->leftjoin('public.kelurahan as kel', 'kel.kelurahan_id', 'p.id_kelurahan')
          ->where(function ($w) {
            return $w->where(function ($w1) {
              return $w1->where('p.tingkat', 'kota')->where('j.id', 3);
            })
              ->orWhere(function ($w2) {
                return $w2->where('p.tingkat', 'kecamatan')->where('j.id', 18);
              })
              ->orWhere(function ($w3) {
                return $w3->where('p.tingkat', 'kelurahan')->where('j.id', 33);
              });
          })
          ->whereNull('p.deleted_at')
          ->selectRaw("
            p.nama,p.tingkat,COALESCE(kec.kecamatan_dispenduk,'') as kecamatan_dispenduk,COALESCE(kel.kelurahan_dispenduk,'') as kelurahan_dispenduk,
            COALESCE(p.path_foto,'') as path_foto,
            COALESCE(p.kata_sambutan,'') as kata_sambutan,
            CASE WHEN p.id_jabatan='3' THEN 0 ELSE CASE WHEN id_jabatan='18' THEN 1 ELSE 2 END END as urutan
            ")
          ->orderByRaw("urutan, kecamatan_dispenduk ASC, kelurahan_dispenduk ASC")
          // ->limit(1)
          // ->offset(2)
          ->get();

        $user = collect($user)->map(function ($item) {
          $image_path = 'https://upload.wikimedia.org/wikipedia/commons/b/ba/City_of_Surabaya_Logo.svg';
          if ($item->path_foto != '') {
            // $header = get_headers('https://dispendik.surabaya.go.id/sibunda_file/' . $item->path_foto, 1);
            // if ($header[0] == 'HTTP/1.1 200 OK') {
            $image_path = 'https://dispendik.surabaya.go.id/sibunda_file/' . $item->path_foto;
            // }
          }

          $wilayah = '';
          if ($item->tingkat == 'kota') {
            $wilayah = 'Kota Surabaya';
          } else if ($item->tingkat == 'kecamatan') {
            $wilayah = 'Kecamatan ' . $item->kecamatan_dispenduk;
          } else {
            $wilayah = 'Kelurahan ' . $item->kelurahan_dispenduk;
          }

          $datanya = [
            'nama' => $item->nama,
            'kata_sambutan' => $item->kata_sambutan,
            'wilayah' => $wilayah,
            'foto' => $image_path,
            'urutan' => $item->urutan,
          ];
          return $datanya;
        })->values()->chunk(100);

        $data = [
          'active_menu' => 'home',
          'active_submenu' => '',
          'user' => $user,
        ];
        return view('landing.dashboard', $data);
        break;
      case 'paud':
        $cari_kecamatan = isset($request->kecamatan) ? $request->kecamatan : '';
        $cari_kelurahan = isset($request->kelurahan) ? $request->kelurahan : '';
        $cari_jenjang = isset($request->jenjang) ? $request->jenjang : '';

        $kecamatan = DB::table('public.kecamatan as kec')
          ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
          ->selectRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,kec.kecamatan_id,kel.kelurahan_id")
          ->orderBy('kec.kecamatan_dispenduk')
          ->get();

        $kecamatan = collect($kecamatan)->groupBy('kecamatan_kode')->map(function ($m, $kecamatan) {
          $baris = $m->first();
          $data = [
            'kecamatan_kode' => $baris->kecamatan_kode,
            'kecamatan_dispenduk' => $baris->kecamatan_dispenduk,
            'kelurahan' => base64_encode(json_encode(collect($m)->sortBy('kelurahan_dispenduk'))),
          ];
          return $data;
        })->values();

        $sekolah = DB::connection('pgsql_paud')->table('public.sekolah as s')
          ->join('public.master_user as u', 'u.username', 's.npsn')
          ->join('public.kecamatan as kec', 'kec.kecamatan_kode', 's.kec_id')
          ->join('public.kelurahan as kel', 'kel.kelurahan_kode', 's.desa')
          ->selectRaw("
            s.npsn,s.nama,kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,
            s.alamat,s.telp,COALESCE(s.logo,'') as logo,
            s.lat_long
            ")
          ->whereIn('s.jenjang', ['SPS', 'TPA', 'TK', 'KB', 'RA'])
          ->when($cari_kecamatan != '', function ($c) use ($cari_kecamatan) {
            return $c->where('kec.kecamatan_kode', $cari_kecamatan);
          })
          ->when($cari_kelurahan != '', function ($c) use ($cari_kelurahan) {
            return $c->where('kel.kelurahan_kode', $cari_kelurahan);
          })
          ->when($cari_jenjang != '', function ($c) use ($cari_jenjang) {
            return $c->where('s.jenjang', $cari_jenjang);
          })
          ->whereRaw("u.user_enable is TRUE")
          ->orderByRaw("kec.kecamatan_dispenduk ASC,kel.kelurahan_dispenduk ASC,s.nama ASC")
          ->paginate(9);

        $npsn = collect($sekolah->items())->pluck('npsn')->toArray();

        $pegawai = DB::connection('pgsql_paud')->table('public.pegawai')
          ->selectRaw("npsn,COUNT(*) as jumlah_pegawai")
          ->whereIn('npsn', $npsn)
          ->where('keterangan', 'Aktif')
          ->whereRaw("induk is true")
          ->groupBy('npsn')
          ->get();

        $siswa = DB::connection('pgsql_paud')->table('public.siswa')
          ->selectRaw("npsn,COUNT(*) as jumlah_siswa")
          ->whereIn('npsn', $npsn)
          ->where('status_siswa', 'Aktif')
          ->whereRaw("alumni is not true")
          ->groupBy('npsn')
          ->get();

        $sekolah->setCollection(
          $sekolah->getCollection()->map(function ($i) use ($siswa, $pegawai) {
            $i->logo = ($i->logo != '') ? 'https://dispendik.surabaya.go.id/profilsekolah_file/logo/' . $i->logo : asset('logo/image-icon.png');

            if ($i->lat_long != '') {
              $i->lat_long = 'https://www.google.com/maps/search/?api=1&query=' . $i->lat_long;
            } else {
              $i->lat_long = 'https://www.google.com/maps/search/?api=1&query=-7.3064409,112.7586384';
            }
            $baris_siswa = collect($siswa)->where('npsn', $i->npsn)->first();
            $baris_pegawai = collect($pegawai)->where('npsn', $i->npsn)->first();
            $i->jumlah_siswa = !empty($baris_siswa) ? $baris_siswa->jumlah_siswa : 0;
            $i->jumlah_pegawai = !empty($baris_pegawai) ? $baris_pegawai->jumlah_pegawai : 0;
            return $i;
          })
        );

        $data = [
          'active_menu' => 'menu_paud',
          'active_submenu' => 'paud',
          'sekolah' => $sekolah,
          'kecamatan' => $kecamatan,
          'cari_kecamatan' => $cari_kecamatan,
          'cari_kelurahan' => $cari_kelurahan,
          'cari_jenjang' => $cari_jenjang,
        ];
        return view('landing.paud.paud', $data);
        break;

      case 'modal_paud':
        $npsn = $request->id;
        $kategori = $request->kategori;
        $judul = 'Detail data ' . $kategori;

        if ($kategori == 'siswa') {
          $baris = DB::connection('pgsql_paud')->table('public.siswa')
            ->selectRaw("nama,CONCAT(SUBSTR(nik,1,3),'********',SUBSTRING(nik FROM length(nik)-1-2 FOR 3),'*') as nik")
            ->where('npsn', $npsn)
            ->where('status_siswa', 'Aktif')
            ->whereRaw("alumni is not true")
            ->orderBy('nama')
            ->get();
        } else {
          $baris = DB::connection('pgsql_paud')->table('public.pegawai')
            ->selectRaw("nama,CONCAT(SUBSTR(no_ktp,1,3),'********',SUBSTRING(no_ktp FROM length(no_ktp)-1-2 FOR 3),'*') as nik")
            ->where('npsn', $npsn)
            ->where('keterangan', 'Aktif')
            ->whereRaw("induk is true")
            ->orderBy('nama')
            ->get();
        }

        $data = [
          'judul' => $judul,
          'data' => $baris,
        ];
        $content = view('landing.paud.modal', $data)->render();
        return ['content' => $content];
        break;

      case 'ijop':
        $cari_kecamatan = isset($request->kecamatan) ? $request->kecamatan : '';
        $cari_kelurahan = isset($request->kelurahan) ? $request->kelurahan : '';
        $cari_jenjang = isset($request->jenjang) ? $request->jenjang : '';

        $kecamatan = DB::table('public.kecamatan as kec')
          ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
          ->selectRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,kec.kecamatan_id,kel.kelurahan_id")
          ->orderBy('kec.kecamatan_dispenduk')
          ->get();

        $kecamatan = collect($kecamatan)->groupBy('kecamatan_kode')->map(function ($m, $kecamatan) {
          $baris = $m->first();
          $data = [
            'kecamatan_kode' => $baris->kecamatan_kode,
            'kecamatan_dispenduk' => $baris->kecamatan_dispenduk,
            'kelurahan' => base64_encode(json_encode(collect($m)->sortBy('kelurahan_dispenduk'))),
          ];
          return $data;
        })->values();

        $data = [
          'active_menu' => 'menu_paud',
          'active_submenu' => 'ijop',
          'kecamatan' => $kecamatan,
          'cari_kecamatan' => $cari_kecamatan,
          'cari_kelurahan' => $cari_kelurahan,
          'cari_jenjang' => $cari_jenjang,
        ];
        return view('landing.ijop', $data);
        break;

      case 'get_ijop':
        $cari_kecamatan = isset($request->kecamatan) ? $request->kecamatan : '';
        $cari_kelurahan = isset($request->kelurahan) ? $request->kelurahan : '';
        $cari_jenjang = isset($request->jenjang) ? $request->jenjang : '';

        $monitoring = DB::table('public.monitoring_ijop')
          ->selectRaw("npsn,hasil,keterangan,path_pernyataan_tutup")
          ->distinct('npsn')
          ->orderByRaw("npsn, id DESC");

        $ijops = DB::table('public.ijops as i')
          ->leftJoinSub($monitoring, 'm', function ($j) {
            return $j->on('m.npsn', '=', 'i.npsn');
          })
          ->selectRaw("
          i.npsn,m.hasil,m.keterangan,m.path_pernyataan_tutup,i.nama_sekolah,i.no_izin_operasional,i.tgl_izin_operasional,i.selesai_berlaku,i.izin_online,
          i.str_mati_ijob_2,i.str_mati_ijob_1,i.str_akan_mati,i.kecamatan_name,i.kelurahan_name
          ")
          ->when($cari_kecamatan != '', function ($w) use ($cari_kecamatan) {
            return $w->where('kecamatan_kode', $cari_kecamatan);
          })
          ->when($cari_kelurahan != '', function ($w) use ($cari_kelurahan) {
            return $w->where('kelurahan_kode', $cari_kelurahan);
          })
          ->when($cari_jenjang != '', function ($w) use ($cari_jenjang) {
            return $w->where('i.jenjang', $cari_jenjang);
          })
          ->orderByRaw("kecamatan_name,kelurahan_name,nama_sekolah");

        return Datatables::of($ijops)
          ->addColumn('status', function ($ijop) {
            if (!$ijop->izin_online) {
              return 'Tidak Terdaftar Ijop';
            } else if (strlen($ijop->str_mati_ijob_2) > 1) {
              return 'Non-Aktif > 1 Th';
            } else if (strlen($ijop->str_mati_ijob_1) > 1) {
              return 'Tidak Aktif';
            } else if (strlen($ijop->str_akan_mati) > 1) {
              return 'Akan Mati < 4 Bln';
            } else {
              return 'Aktif';
            }
          })
          ->addColumn('waktu', function ($ijop) {
            return Waktu::parseFullDate($ijop->selesai_berlaku);
          })
          ->addColumn('hasil_monitoring', function ($ijop) {
            return $ijop->hasil;
          })
          ->addColumn('keterangan_monitoring', function ($ijop) {
            if ($ijop->hasil == 'Tutup') {
              return '<a class="btn btn-sm btn-primary" href="https://dispendik.surabaya.go.id/sibunda_file/' . $ijop->path_pernyataan_tutup . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Lihat File</a>';
            } else {
              return $ijop->keterangan;
            }
          })
          ->setRowClass(function ($ijop) {
            if (!$ijop->izin_online || strlen($ijop->str_mati_ijob_2) > 1 || strlen($ijop->str_mati_ijob_1) > 1) {
              return 'bg-danger blink';
            } else if (strlen($ijop->str_akan_mati) > 1) {
              return 'bg-warning blink';
            } else {
              return '';
            }
          })
          ->rawColumns(['nama_sekolah', 'keterangan_monitoring'])
          ->make();
        break;

      case 'perbarui_ijop':
        return $this->perbarui_ijop($request);
        break;
      case 'buku':
        $buku = DB::table('public.buku as s')
          ->orderBy('id', 'DESC')
          ->paginate(9);

        $data = [
          'active_menu' => 'literasi',
          'active_submenu' => 'buku',
          'buku' => $buku,
        ];
        return view('landing.buku', $data);
        break;
      case 'berkreasi':
        $data = [
          'active_menu' => 'literasi',
          'active_submenu' => 'berkreasi',
        ];
        return view('landing.berkreasi', $data);
        break;
      case 'dashboard':
        return Redirect::route('landing', ['home']);
        break;

      case 'kegiatan':
        $kegiatan = DB::table('public.kegiatan as k')
          ->join('public.program as p', 'p.id_program', 'k.program_id')
          ->join('public.users as u', 'u.username', 'p.updated_by')
          ->selectRaw("
            k.id_kegiatan as id,p.nama_kegiatan as nama_program,k.nama_kegiatan,k.tgl_kegiatan,
            u.nama,k.materi,k.foto
          ")
          ->orderByRaw("k.tgl_kegiatan DESC")
          ->whereNull('u.deleted_at')
          ->paginate(9);

        $data = [
          'active_menu' => 'kegiatan',
          'active_submenu' => '',
          'kegiatan' => $kegiatan,
        ];
        return view('landing.kegiatan', $data);
        break;
      case 'mitra':
        $cari = (isset($request->nama_mitra)) ? $request->nama_mitra : '';
        $mitra = DB::table('public.mitra')
          ->whereIn('status_aktif', ['Pengajuan', 'Setujui'])
          ->when($cari != '', function ($w) use ($cari) {
            return $w->where('nama_mitra', 'ilike', '%' . $cari . '%');
          })->orderByRaw("CASE WHEN status_aktif='Pengajuan' THEN 5 ELSE 0 END, created_at DESC")->paginate(9);
        $data = [
          'cari' => $cari,
          'mitra' => $mitra,
        ];
        return view('landing.mitra.mitra', $data);
        break;
      case 'form_mitra':
        $id = $request->id;
        $judul = ($id == 0) ? 'Tambah Mitra' : 'Edit Mitra';

        $kecamatan = DB::table('public.kecamatan as kec')
          ->join('public.kelurahan as kel', 'kel.kecamatan_kode', 'kec.kecamatan_kode')
          ->selectRaw("kec.kecamatan_dispenduk,kel.kelurahan_dispenduk,kec.kecamatan_kode,kel.kelurahan_kode,kec.kecamatan_id,kel.kelurahan_id")
          ->orderBy('kec.kecamatan_dispenduk')
          ->get();

        $kecamatan = collect($kecamatan)->groupBy('kecamatan_kode')->map(function ($m, $kecamatan) {
          $baris = $m->first();
          $data = [
            'kecamatan_kode' => $baris->kecamatan_kode,
            'kecamatan_dispenduk' => $baris->kecamatan_dispenduk,
            'kelurahan' => base64_encode(json_encode(collect($m)->sortBy('kelurahan_dispenduk'))),
          ];
          return $data;
        })->values();

        $data = [
          'judul' => $judul,
          'kecamatan' => $kecamatan,
        ];
        $content = view('landing.mitra.form_mitra', $data)->render();
        return ['content' => $content];
        break;
      case 'daftar_mitra':
        $username = $request->username; //indomaret50"
        $password = $request->password; //123456"
        $nama = $request->nama; //Indomaret Kedung Cowek"
        $kecamatan = $request->kecamatan; //1205"
        $kelurahan = $request->kelurahan; //120503"
        $alamat = $request->alamat; //ISTANBUL"
        $nama_kepala = $request->nama_kepala; //ISTANBUL"
        $kontak = $request->kontak; //ISTANBUL"
        $validator = Validator::make($request->all(), [
          'username' => [
            'required',
            'string',
            'max:50',
            Rule::unique('pgsql.public.users', 'username'),
          ],
          [
            // custom pesan
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah terdaftar, silakan gunakan username lain.',
            'username.max'      => 'Username maksimal 50 karakter.',
          ]
        ]);

        if ($validator->fails()) {
          return Redirect::route('landing', ['mitra'])
            ->withErrors($validator);
        }

        $simpan_user = DB::table('public.users')->insertGetId([
          'username' => $username,
          'password' => Hash::make($password),
          'nama' => $nama,
          'role' => 'mitra',
          'tingkat' => 'kota',
          'id_jabatan' => '3',
        ], 'id');

        $simpan_mitra = DB::table('public.mitra')->insert([
          'nama_mitra' => $nama,
          'alamat' => $alamat,
          'kecamatan' => $kecamatan,
          'kelurahan' => $kelurahan,
          'status_aktif' => 'Pengajuan',
          'user_id' => $simpan_user,
          'created_by' => $simpan_user,
          'nama_kepala' => $nama_kepala,
          'kontak' => $kontak,
        ]);

        return Redirect::route('landing', ['mitra'])->with('message', 'Berhasil disimpan dengan status Pengajuan');
        break;
      case 'sewa_gedung':
        return view('landing.sewa_gedung.index');
        break;
      case 'get_jadwal':
        $tanggal = $request->tanggal;
        $awal = date('Y-m-01', strtotime($request->tanggal));
        $akhir = date('Y-m-t', strtotime($request->tanggal));

        $warna = [];

        $sewa = DB::table('public.sewa_graha')
          ->whereRaw("tanggal BETWEEN '$awal' AND '$akhir'")
          ->orderByRaw("tanggal,jam,CASE WHEN status='Setujui' THEN 0 ELSE 1 END")
          ->get();

        $hari_ini = collect($sewa)->where('tanggal', $tanggal);
        $kalender = collect($sewa)->groupBy('tanggal')->map(function ($item, $tanggal) use (&$warna) {
          $jumlah = $item->count();
          if (!isset($warna[$jumlah])) {
            $hex = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $warna[$jumlah] = $hex;
          }
          return [
            'tanggal' => date('d', strtotime($tanggal)),
            'jumlah' => $jumlah,
            'warna' => $warna[$jumlah],
          ];
        })->values();

        $data = [
          'sewa' => $sewa,
          'hari_ini' => $hari_ini,
        ];

        $content = view('landing.sewa_gedung.jadwal', $data)->render();
        return [
          'content' => $content,
          'kalender' => $kalender,
        ];
        break;
      case 'simpan_sewa':
        $kegiatan = $request->kegiatan; //"Wedding Private Party"
        $kontak = $request->kontak; //"08596452256572"
        $penyewa = $request->penyewa; //"Antonio"
        $tanggal = date('Y-m-d', strtotime($request->tanggal)); //"2026-02-28"
        $waktu = date('H:i', strtotime($request->waktu)); //"19:00"
        $waktu_awal = date('H:i', strtotime('-59 minute', strtotime($request->waktu))); //"19:00"
        $waktu_akhir = date('H:i', strtotime('+59 minute', strtotime($request->waktu))); //"19:00"

        $cek = DB::table('public.sewa_graha')
          ->where('tanggal', $tanggal)
          ->whereRaw("jam BETWEEN '$waktu_awal' AND '$waktu_akhir'")
          ->where('status', 'Setujui')
          ->get();
        if ($cek->count() == 0) {
          $data_simpan = [
            'kegiatan' => $kegiatan,
            'kontak' => $kontak,
            'penyewa' => $penyewa,
            'tanggal' => $tanggal,
            'jam' => $waktu,
            'status' => 'Pengajuan',
          ];

          $simpan = DB::table('public.sewa_graha')->insert($data_simpan);
          if ($simpan) {
            return ['code' => 200, 'pesan' => 'Berhasil disimpan', 'tanggal' => $tanggal];
          } else {
            return ['code' => 300, 'pesan' => 'Gagal disimpan'];
          }
        } else {
          $waktu_ada = '';
          foreach ($cek as $c) {
            $waktu_ada .= '<li>' . $c->jam . '</li>';
          }
          return ['code' => 300, 'pesan' => 'Terdapat jadwal yang telah disetujui pada tanggal ' . $tanggal . '. Pada jam <ol>' . $waktu_ada . '</ol>'];
        }
        break;
      default:
        return '404 - Halaman tidak ditemukan';
        break;
    }
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
