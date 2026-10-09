<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProfilSekolah\Sekolah;

class MonitoringIjop extends Model
{
  use HasFactory;
  public $connection = 'pgsql';
  protected $table = 'monitoring_ijop';

  protected $fillable = [
    'tingkat',
    'id_kecamatan',
    'id_kelurahan',
    'tanggal',
    'id_petugas',
    'npsn',
    'hasil',
    'keterangan',
    'path_pernyataan_tutup',
    'created_at',
    'updated_at',
  ];

  public function Petugas()
  {
    return $this->belongsTo(ProfilPokja::class, 'id_petugas', 'id')->withTrashed();
  }

  public function Sekolah()
  {
    return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
  }

  public function getKeterangan()
  {
    if ($this->hasil == 'Tutup') {
      return '<a class="btn btn-sm btn-primary" href="https://dispendik.surabaya.go.id/sibunda_file/' . $this->path_pernyataan_tutup . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Lihat File</a>';
    } else {
      return $this->keterangan;
    }
  }
}
