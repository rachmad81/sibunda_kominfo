<?php

namespace App\Tools;

class Bantuan
{
  public static function getRoleLabel($role)
  {
    if ($role == 'dinas') {
      return '<label class="badge badge-danger">Dinas</label>';
    } elseif ($role == 'bunda') {
      return '<label class="badge badge-primary">Bunda</label>';
    } elseif ($role == 'pokja') {
      return '<label class="badge badge-warning">Kelompok Kerja (Pokja)</label>';
    } elseif ($role == 'opd') {
      return '<label class="badge badge-success">Opd (Dinas)</label>';
    } elseif ($role == 'penilai') {
      return '<label class="badge" style="background: #d1fa1b">Penilai</label>';
    } elseif ($role == 'k3_paud') {
      return '<label class="badge" style="background: #fa1bfa">K3 PAUD</label>';
    } elseif ($role == 'paud_sd') {
      return '<label class="badge" style="background: #f73636">Forum Komunikasi PAUD SD</label>';
    } elseif ($role == 'gugus') {
      return '<label class="badge" style="background: #c9a0a0">Gugus Tugas</label>';
    }
    return;
  }

  public static function getStatusLabel($status, $keterangan = null)
  {
    if ($status == \Config::get('variable.status.pencairan_dana.rejected')) {
      echo '<label class="badge badge-danger">Revisi</label>';
    } elseif ($status == \Config::get('variable.status.pencairan_dana.waiting_verif')) {
      echo '<label class="badge badge-info">Verifikasi Staf</label>';
    } elseif ($status == \Config::get('variable.status.pencairan_dana.waiting_kasi')) {
      echo '<label class="badge badge-primary">Verifikasi Kasi</label>';
    } elseif ($status == \Config::get('variable.status.pencairan_dana.waiting_kasubbag_umpeg')) {
      echo '<label class="badge badge-warning">Verifikasi Kasubbag<br>Umpeg</label>';
    } elseif ($status == \Config::get('variable.status.pencairan_dana.waiting_sekretaris')) {
      echo '<label class="badge badge-secondary">Verifikasi Sekretaris</label>';
    } elseif ($status == \Config::get('variable.status.pencairan_dana.waiting_kadis')) {
      echo '<label class="badge badge-info">Verifikasi Kadis</label>';
    } elseif ($status == \Config::get('variable.status.pencairan_dana.accepted')) {
      echo '<label class="badge badge-success">Disetujui</label>';
    }
    if ($keterangan && strlen($keterangan->keterangan) > 1) {
      echo '<br>(' . $keterangan->keterangan . ')';
    }
    return;
  }

  public static function ribuan($angka)
  {
    $hasil = number_format((float) $angka, 0, ',', '.');
    return $hasil;
  }

  public static function cek_norek($norek)
  {
    $base_url = 'https://apps.bankjatim.co.id/api/prod/v/ErCheck';
    $ch = curl_init($base_url);
    #$data='0012022557';
    $payload = json_encode(array("Account" => $norek));

    //attach encoded JSON string to the POST fields
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    //set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

    //return response instead of outputting
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute the POST request
    $result = curl_exec($ch);

    //close cURL resource
    curl_close($ch);

    $data = json_decode($result, true);
    return $data;
  }

  public static function cek_nik($nik)
  {

    if (in_array($_SERVER['REMOTE_ADDR'], ['192.168.0.18', '127.0.0.1', '::1'])) {
      $base_url = 'http://10.255.160.250:5070/nik_wni_v2.php?username=dispendik_05&password=dispendik_05&id=' . $nik;
    } else {
      $base_url = 'http://103.76.17.2:999/cek_nik.php?xyz=' . $nik;
    }

    $ch = curl_init($base_url);

    //set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

    //return response instead of outputting
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute the POST request
    $result = curl_exec($ch);

    //close cURL resource
    curl_close($ch);

    $data = json_decode($result, true);

    return $data;
  }

  public static function numberToText($number)
  {
    $nilai = abs($number);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
      $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
      $temp = self::numberToText($nilai - 10) . " Belas ";
    } else if ($nilai < 100) {
      $temp = self::numberToText($nilai / 10) . " Puluh " . self::numberToText($nilai % 10);
    } else if ($nilai < 200) {
      $temp = " Seratus " . self::numberToText($nilai - 100);
    } else if ($nilai < 1000) {
      $temp = self::numberToText($nilai / 100) . " Ratus " . self::numberToText($nilai % 100);
    } else if ($nilai < 2000) {
      $temp = " Seribu " . self::numberToText($nilai - 1000);
    } else if ($nilai < 1000000) {
      $temp = self::numberToText($nilai / 1000) . " Ribu " . self::numberToText($nilai % 1000);
    } else if ($nilai < 1000000000) {
      $temp = self::numberToText($nilai / 1000000) . " Juta " . self::numberToText($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
      $temp = self::numberToText($nilai / 1000000000) . " Milyar " . self::numberToText(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
      $temp = self::numberToText($nilai / 1000000000000) . " Trilyun " . self::numberToText(fmod($nilai, 1000000000000));
    }
    return trim($temp);
  }

  public static function validate_file($path, $type)
  {
    // if ($type == 'gambar') {
    //   $extention = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml'];
    // } else if ($type == 'pdf') {
    //   $extention = [];
    // }
    $extention = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml', 'application/pdf'];

    $mimeType = $path->getClientMimeType();

    if (in_array($mimeType, $extention)) {
      return true;
    } else {
      return false;
    }
  }

  public static function checkDrivePermission($url)
  {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER         => true,
      CURLOPT_NOBODY         => true,  // jangan ambil body, cukup header
      CURLOPT_FOLLOWLOCATION => false, // jangan auto redirect
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

    curl_close($ch);

    if ($httpCode == 200) {
      return "PUBLIC";
    } elseif ($httpCode == 302 || $httpCode == 301) {
      if (strpos($response, "accounts.google.com") !== false) {
        return "Restricted / Butuh login";
      }
    }

    return "Link rusak atau tidak valid";
  }

  function getInisial($nama)
  {
    // 1. Ambil tiap kata dari nama dan masukkan ke dalam array
    $kata = explode(" ", $nama);
    $inisial = "";

    if (count($kata) >= 2) {
      // Jika nama terdiri dari 2 kata atau lebih, ambil huruf pertama dari 2 kata pertama
      $inisial = substr($kata[0], 0, 1) . substr($kata[1], 0, 1);
    } else {
      // Jika hanya 1 kata, ambil 2 huruf pertama (atau 1 huruf saja sesuai selera)
      $inisial = substr($kata[0], 0, 2);
    }

    // Ubah ke huruf kapital
    return strtoupper($inisial);
  }
}
