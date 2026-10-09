<?php

namespace App\Http\Libraries;

/**
 *
 */
class Convert
{
  public static function huruf($angka)
  {
    switch ($angka) {
      case '1':
        $huruf = 'Jan';
        break;
      case '2':
        $huruf = 'Feb';
        break;
      case '3':
        $huruf = 'Mar';
        break;
      case '4':
        $huruf = 'Apr';
        break;
      case '5':
        $huruf = 'Mei';
        break;
      case '6':
        $huruf = 'Jun';
        break;
      case '7':
        $huruf = 'Jul';
        break;
      case '8':
        $huruf = 'Agu';
        break;
      case '9':
        $huruf = 'Sep';
        break;
      case '10':
        $huruf = 'Okt';
        break;
      case '11':
        $huruf = 'Nov';
        break;
      case '12':
        $huruf = 'Des';
        break;

      default:
        $huruf = '';
        break;
    }

    return $huruf;
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

  public static function getHari($hari_angka)
  {
    switch ($hari_angka) {
      case 'Sun':
        $hari = 'Minggu';
        break;
      case 'Mon':
        $hari = 'Senin';
        break;
      case 'Tue':
        $hari = 'Selasa';
        break;
      case 'Wed':
        $hari = 'Rabu';
        break;
      case 'Thu':
        $hari = 'Kamis';
        break;
      case 'Fri':
        $hari = 'Jumat';
        break;
      case 'Sat':
        $hari = 'Sabtu';
        break;

      default:
        $hari = '';
        break;
    }

    return $hari;
  }

  public static function fullDate($tanggal)
  {
    $bulan = date('m', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));
    $hari = date('d', strtotime($tanggal));
    switch ($bulan) {
      case '1':
        $huruf = 'Januari';
        break;
      case '2':
        $huruf = 'Februari';
        break;
      case '3':
        $huruf = 'Maret';
        break;
      case '4':
        $huruf = 'April';
        break;
      case '5':
        $huruf = 'Mei';
        break;
      case '6':
        $huruf = 'Juni';
        break;
      case '7':
        $huruf = 'Juli';
        break;
      case '8':
        $huruf = 'Agustus';
        break;
      case '9':
        $huruf = 'September';
        break;
      case '10':
        $huruf = 'Oktober';
        break;
      case '11':
        $huruf = 'November';
        break;
      case '12':
        $huruf = 'Desember';
        break;

      default:
        $huruf = '';
        break;
    }

    return
      [
        'hari' => $hari,
        'bulan' => $huruf,
        'tahun' => $tahun,
      ];
  }
}
