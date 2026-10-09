<?php

namespace App\Models\Paudhi;

use App\Models\PaudHI\PengajuanPaudHI as PaudHIPengajuanPaudHI;
use Illuminate\Database\Eloquent\Model;

class InstrumenPengajuan extends Model
{
  protected $table = 'paud_hi.instrumen_pengajuan';
  protected $connection = 'pgsql_paud';
  public $timestamps = false;

  protected $fillable = ['pengajuan_paud_hi', 'instrumen_id', 'nama', 'hasil_pemantauan', 'file', ' komentar'];

  public function PengajuanPaudHI()
  {
    return $this->belongsTo(PaudHIPengajuanPaudHI::class, 'instrumen_id', 'id');
  }

  public function Instrumen()
  {
    return $this->belongsTo(Instrumen::class, 'instrumen_id', 'id');
  }
}
