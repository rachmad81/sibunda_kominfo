<?php

namespace App\Http\Libraries;

use DB;

class GetData
{
  public static function get_kelurahan($kec_id)
  {
    $kelurahan = DB::connection('pgsql_paud')->table('public.kelurahan')->where('kecamatan_kode', $kec_id)->orderBy('kelurahan_dispenduk', 'ASC')->get();

    return $kelurahan;
  }

  public static function jabatan($role)
  {
    if ($role == 'k3_paud') {
      $jabatan = [
        'Ketua',
        'Sekretaris',
        'Bidang Perencanaan dan Pelaksanaan Program',
        'Bidang Pengembangan Karir dan Profesi',
        'Bidang Administrasi, Organisasi dan Sarpras',
        'Humas'
      ];
    } else if ($role == 'paud_sd') {
      $jabatan = [
        'Penasihat',
        'Pembina I',
        'Pembina II',
        'Ketua',
        'Sekretaris',
        'Ketua Bidang Pendidikan dan Pelatihan',
        'Ketua Bidang Humas dan Kerjasama',
        'Ketua Bidang Sosial',
        'Ketua Bidang Sumber Daya Manusia',
      ];
    } else {
      $jabatan = [];
    }

    return $jabatan;
  }
}
