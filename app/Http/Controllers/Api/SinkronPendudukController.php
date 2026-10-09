<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class SinkronPendudukController extends Controller
{
  function halaman(Request $request)
  {
    $halaman = $request->halaman;

    switch ($halaman) {
      case 'main':
        $ofset = $request->ofset;
        $penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject')
          ->selectRaw("nik")
          ->where('is_aktif_kependudukan', true)
          ->where('status_keberadaan', 'ADA')
          ->where('kabupaten', 'KOTA SURABAYA')
          ->where(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),tgl_lahir))"), '6')
          ->orderBy('nik')
          ->offset($ofset)
          ->limit(10)
          ->get();

        $data = [
          'penduduk' => $penduduk,
          'ofset' => $ofset,
        ];
        return view('api.sinkron_penduduk', $data);
        break;

      case 'get_data':
        $ofset = $request->ofset;
        return $penduduk = DB::connection('db_cutt_off')->table('public.penduduk_inject')
          ->selectRaw("nik")
          ->where('is_aktif_kependudukan', true)
          ->where('status_keberadaan', 'ADA')
          ->where('kabupaten', 'KOTA SURABAYA')
          ->where(DB::raw("EXTRACT(YEAR FROM AGE(NOW(),tgl_lahir))"), '6')
          ->paginate(10);
        break;

      case 'halaman_sinkron':
        return view('api.sinkron.main');
        break;

      case 'get_nik':
        $id = $request->id;
        $usia = $request->usia;

        $arr_kec =  ['PABEAN CANTIAN', 'KARANGPILANG', 'GAYUNGAN', 'WIYUNG', 'BULAK', 'ASEMROWO', 'KENJERAN', 'GUBENG', 'JAMBANGAN', 'LAKAR SANTRI', 'MULYOREJO', 'WONOKROMO', 'RUNGKUT', 'TAMBAKSARI', 'SUKOMANUNGGAL', 'KARANG PILANG', 'GUNUNG ANYAR', 'TEGALSARI', 'KREMBANGAN', 'SAMBI KEREP', 'BENOWO', 'SAWAHAN', 'PAKAL', 'BUBUTAN', 'WONOCOLO', 'TANDES', 'SIMOKERTO', 'ASEM ROWO', 'SEMAMPIR', 'SUKOLILO', 'LAKARSANTRI', 'GENTENG', 'SAMBIKEREP', 'TENGGILIS MEJOYO', 'DUKUH PAKIS'];
        $arr_kel = ['DUKUH PAKIS', 'PERAK TIMUR', 'LONTAR', 'MANUKAN KULON', 'PUTAT JAYA', 'KARANGPILANG', 'SIMOMULYO BARU', 'JEPARA', 'WIYUNG', 'KENDANGSARI', 'BULAK', 'SIDODADI', 'NGAGEL REJO', 'ASEMROWO', 'WARUGUNUNG', 'NGAGEL', 'SUMBER REJO', 'RANGKAH', 'AIRLANGGA', 'KLAMPIS NGASEM', 'SIDOTOPO', 'PAGESANGAN', 'MANUKAN WETAN', 'SONOKWIJENAN', 'JAMBANGAN', 'KARANG POH', 'RUNGKUT MENANGGAL', 'PLOSO', 'KAPASMADYA BARU', 'SEMEMI', 'KEJAWAN PUTIH TAMBAK', 'SUKOMANUNGGAL', 'TAMBAKSARI', 'DUKUH SUTOREJO', 'ALUN-ALUN CONTONG', 'GUNDIH', 'PERAK UTARA', 'KREMBANGAN SELATAN', 'SIDOTOPO WETAN', 'MENUR PUMPUNGAN', 'PENJARINGAN SARI', 'SAWUNGGALING', 'KEDURUS', 'GUNUNG ANYAR', 'TAMBAKREJO', 'TANJUNG PERAK', 'TEGALSARI', 'KANDANGAN', 'KREMBANGAN UTARA', 'RUNGKUT TENGAH', 'PERAK BARAT', 'TAMBAK OSOWILANGUN', 'JERUK', 'MOROKREMBANGAN', 'SAWAHAN', 'KALISARI', 'SIWALANKERTO', 'KEBRAON', 'PAKAL', 'MARGOREJO', 'KETINTANG', 'PEGIRIAN', 'TANAH KALI KEDINDING', 'WONOREJO', 'KUPANG KRAJAN', 'KARANGPOH', 'AMPEL', 'SIMOKERTO', 'PACAR KELING', 'PACAR KEMBANG', 'LIDAH WETAN', 'TAMBAK OSO WILANGUN', 'DR. SOETOMO', 'NGINDEN JANGKUNGAN', 'LAKARSANTRI', 'GENTENG', 'KUTISARI', 'SAMBIKEREP', 'DUKUH KUPANG', 'TENGGILIS MEJOYO', 'PRADAH KALIKENDAL', 'PETEMON', 'GADING', 'MADE', 'JAJAR TUNGGAL', 'RUNGKUT KIDUL', 'SIDOSERMO', 'BALONGSARI', 'WARU GUNUNG', 'GAYUNGAN', 'SUMUR WELUT', 'JAJARTUNGGAL', 'SEMOLOWARU', 'BRINGIN', 'KEBONSARI', 'DUPAK', 'MEDOKAN AYU', 'BANYU URIP', 'KEPUTRAN', 'KENJERAN', 'GUBENG', 'BARATAJAYA', 'JAGIR', 'KARAH', 'KEDUNGDORO', 'GEBANG PUTIH', 'KAPASAN', 'BENDUL MERISI', 'MULYOREJO', 'TEMBOK DUKUH', 'WONOKROMO', 'KALIRUNGKUT', 'JEMUR WONOSARI', 'SUKOLILO BARU', 'SIMOMULYO', 'PAKIS', 'KEPUTIH', 'WONOKUSUMO', 'TANJUNGSARI', 'MOJO', 'SIMOLAWANG', 'KETABANG', 'MENANGGAL', 'KARANG PILANG', 'DR. SUTOMO', 'BANJAR SUGIHAN', 'MEDOKAN SEMAMPIR', 'BABATAN', 'SUMURWELUT', 'PUTAT GEDE', 'GENTING KALIANAK', 'DUKUH MENANGGAL', 'PENJARINGANSARI', 'GUNUNGSARI', 'KEDUNG BARUK', 'BONGKARAN', 'NYAMPLUNGAN', 'PUCANG SEWU', 'GUNUNG SARI', 'EMBONG KALIASIN', 'NGAGELREJO', 'BALAS KLUMPRIK', 'SUKO MANUNGGAL', 'KERTAJAYA', 'KALIJUDAN', 'GUNUNG ANYAR TAMBAK', 'PACARKEMBANG', 'PACARKELING', 'KEMAYORAN', 'BABAT JERAWAT', 'PANJANG JIWO', 'PRADAHKALIKENDAL', 'BENOWO', 'KAPASARI', 'PENELEH', 'KEDUNG COWEK', 'DARMO', 'ROMOKALISARI', 'BUBUTAN', 'UJUNG', 'TANDES', 'MANYAR SABRANGAN', 'SONO KWIJENAN', 'BULAK BANTENG', 'ASEM ROWO', 'DUKUH SETRO', 'ALON ALON CONTONG', 'BANGKINGAN', 'LIDAH KULON', 'TAMBAK WEDI', 'BERINGIN', 'TAMBAK SARIOSO'];

        $basequery = DB::connection('db_cutt_off')
          ->table('public.penduduk_inject as a')
          ->leftJoin('public.survey_bunda as b', 'b.nik', 'a.nik')
          ->selectRaw("a.nik,EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir)) as usia")
          ->whereRaw("(a.is_aktif_kependudukan IS TRUE AND a.status_keberadaan='ADA')")
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kecamatan_dom,''),a.kecamatan)"), $arr_kec)
          ->whereIn(DB::raw("COALESCE(NULLIF(a.kelurahan_dom,''),a.kelurahan)"), $arr_kel)
          ->when($usia !== 'All', function ($w) use ($usia) {
            return $w->whereRaw("(EXTRACT(YEAR FROM AGE(NOW(),a.tgl_lahir))=$usia)");
          });

        switch ($id) {
          case '0':
            $sql_get = (clone $basequery)
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '1':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              b.status_sekolah IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              OR (COALESCE(a.nama_sekolah,'')<>'' AND a.is_aktif_pendidikan IS TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') <> ''
              AND a.is_aktif_pendidikan IS TRUE
              )
              END
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '2':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '3':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END
              AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '4':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'sesuai'
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '5':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'pindah_dalam'
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '6':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'pindah_luar'
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '7':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'tidak_ditemukan'
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '8':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'data_ganda'
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '9':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') ilike 'meninggal'
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '10':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND (COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.status_alamat,'') not ilike all(array['sesuai','pindah_%','tidak_ditemukan','data_ganda','meninggal'])
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '11':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 1%')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '12':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 2%')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '13':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 3%')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '14':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 4%')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '15':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') ILIKE '%Faktor 5%')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '16':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END AND ((COALESCE(b.nik,'') <> '' AND COALESCE(b.status_verifikasi,'')='Selesai') AND COALESCE(b.detail_tidak_sekolah,'') NOT ILIKE ALL(ARRAY['%Faktor 5%','%Faktor 4%','%Faktor 3%','%Faktor 2%','%Faktor 1%']))
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;
          case '17':
            $sql_get = (clone $basequery)
              ->whereRaw("
              CASE
              WHEN COALESCE(b.nik,'') <> '' THEN
              (
              COALESCE(b.status_sekolah,'') NOT IN ('Sudah','sudah_paud','sudah_sd','Akan sekolah','akan_daftar')
              AND (COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE)
              )
              ELSE
              (
              COALESCE(a.nama_sekolah,'') = ''
              OR a.is_aktif_pendidikan IS NOT TRUE
              )
              END
              AND (COALESCE(b.nik,'') = '' OR COALESCE(b.status_verifikasi,'')!='Selesai')
              ")
              ->orderBy('usia')
              ->limit(20)->get();
            break;

          default:
            $sql_get = "";
            break;
        }

        $nik = collect($sql_get)->pluck('nik')->toArray();
        return $nik;
        break;

      default:
        # code...
        break;
    }
  }
}
