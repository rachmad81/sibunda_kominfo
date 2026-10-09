<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApresiasiPengajuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apresiasi_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('tingkat',15);
            $table->unsignedInteger('id_kecamatan')->nullable();
            $table->unsignedInteger('id_kelurahan')->nullable();
            $table->unsignedInteger('id_bunda');
            $table->string('nama',250);
            $table->string('email');
            $table->string('alamat',500);
            $table->string('no_telp',20);
            $table->string('status',30)->default('Draft');
            $table->string('keterangan',150)->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
        });

        Schema::create('apresiasi_pengajuan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_pengajuan');
            $table->unsignedInteger('id_komponen');
        });

        Schema::create('apresiasi_pengajuan_detail_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_pengajuan_detail');
            $table->unsignedInteger('id_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apresiasi_pengajuan');
        Schema::dropIfExists('apresiasi_pengajuan_detail');
        Schema::dropIfExists('apresiasi_pengajuan_detail_kegiatan');
    }
}
