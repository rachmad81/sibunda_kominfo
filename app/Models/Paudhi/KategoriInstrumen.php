<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class KategoriInstrumen extends Model
{
  protected $table = 'paud_hi.kategori_instrumen';
  protected $connection = 'pgsql_paud';

  protected $fillable = ['tahun', 'parent_id', 'order', 'nama'];

  public function Instrumen()
  {
    return $this->hasMany(Instrumen::class, 'kategori_id', 'id');
  }

  public function Parent()
  {
    return $this->belongsTo(KategoriInstrumen::class, 'parent_id', 'id');
  }

  public function Children()
  {
    return $this->hasMany(KategoriInstrumen::class, 'parent_id', 'id');
  }
}
