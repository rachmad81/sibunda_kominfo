<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIjopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ijops', function (Blueprint $table) {
            $table->string('no_izin_operasional',100)->nullable();
            $table->string('tgl_izin_operasional',100)->nullable();
            $table->string('mulai_berlaku',100)->nullable();
            $table->string('selesai_berlaku',100)->nullable();
            $table->string('izin_online',100)->nullable();
            $table->string('nama_sekolah',100)->nullable();
            $table->string('npsn',100)->nullable();
            $table->string('kecamatan_name',100)->nullable();
            $table->string('kecamatan_kode',100)->nullable();
            $table->string('kelurahan_name',100)->nullable();
            $table->string('kelurahan_kode',100)->nullable();
            $table->string('order_izin_online',100)->nullable();
            $table->string('str_izin_online',100)->nullable();
            $table->string('order_mati_ijob_2',100)->nullable();
            $table->string('str_mati_ijob_2',100)->nullable();
            $table->string('order_mati_ijob_1',100)->nullable();
            $table->string('str_mati_ijob_1',100)->nullable();
            $table->string('order_akan_mati',100)->nullable();
            $table->string('str_akan_mati',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ijops');
    }
}
