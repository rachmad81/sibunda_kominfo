<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JabatanPokja;

class JabatanPokjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jabatan_khusus['kota'] = [
            'Wali Kota - Pembina',
            'Kepala Dinas - Pembina Teknis',
        ];

        $jabatan_khusus['kecamatan'] = [
            'Camat - Pembina',
            'Penilik PAUD - Pembina Teknis',
        ];

        $jabatan_khusus['kelurahan'] = [
            'Lurah - Pembina',
            'Dikmas Kecamatan - Penanggungjawab Teknis',
        ];

        $jabatan_umum = [
            'Bunda PAUD - Penanggungjawab',
            'Ketua',
            'Wakil Ketua',
            'Sekretaris',
            'Bendahara',
            'Ketua Bidang Pendidikan',
            'Anggota Bidang Pendidikan',
            'Ketua Bidang Kesehatan dan Gizi',
            'Anggota Bidang Kesehatan dan Gizi',
            'Ketua Bidang Pengasuhan dan Kesejahteraan Anak',
            'Anggota Bidang Pengasuhan dan Kesejahteraan Anak',
            'Ketua Bidang Pemenuhan Hak dan Perlindungan Anak',
            'Anggota Bidang Pemenuhan Hak dan Perlindungan Anak'
        ];

        foreach ($jabatan_khusus as $key => $value) {
            foreach ($value as $key1 => $value1) {
                JabatanPokja::create([
                    'tingkat'       => $key,
                    'nama_jabatan'  => $value1
                ]);
            }
            foreach ($jabatan_umum as $key2 => $value2) {
                JabatanPokja::create([
                    'tingkat'       => $key,
                    'nama_jabatan'  => $value2
                ]);
            }
        }
    }
}
