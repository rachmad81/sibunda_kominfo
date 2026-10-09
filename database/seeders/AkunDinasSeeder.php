<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AkunDinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'nama'          => 'Dinas Pendidikan',
            'username'      => 'dinas',
            'password'      => Hash::make('dinasbaik'),
            'role'          => 'dinas',
            'tingkat'       => 'kota',
            'created_at'    => date('Y-m-d H:i:s')
        ]);
    }
}
