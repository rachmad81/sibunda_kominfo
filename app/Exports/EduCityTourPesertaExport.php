<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EduCityTourPesertaExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
  protected $rows;

  public function __construct(Collection $rows)
  {
    $this->rows = $rows;
  }

  public function collection()
  {
    return $this->rows;
  }

  public function headings(): array
  {
    return [
      'Nama Peserta Didik',
      'NIK',
      'Kelas',
      'NPSN Sekolah',
      'Nama Sekolah',
      'Tanggal',
      'Nama Tempat Wisata',
    ];
  }

  public function title(): string
  {
    return 'Rekap Peserta';
  }
}
