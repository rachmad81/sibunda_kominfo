<?php

namespace App\Models\Paudhi;

use Illuminate\Database\Eloquent\Model;

class Instrumen extends Model
{
  protected $table = 'paud_hi.instrumen';
  protected $connection = 'pgsql_paud';

  protected $fillable = ['tahun', 'kategori_id', 'parent_id', 'order', 'is_fillable', 'has_dynamic_children', 'nama'];

  public function Kategori()
  {
    return $this->belongsTo(KategoriInstrumen::class, 'kategori_id', 'id');
  }

  public function Parent()
  {
    return $this->belongsTo(Instrumen::class, 'parent_id', 'id');
  }

  public function Children()
  {
    return $this->hasMany(Instrumen::class, 'parent_id', 'id');
  }
}
