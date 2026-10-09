<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogError extends Model
{
    use HasFactory;
    protected $table = 'log_error';
    public $timestamps = false;
    protected $fillable = [
        'fungsi',
        'lokasi',
        'pesan',
        'created_by',
        'data_requested'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

}
