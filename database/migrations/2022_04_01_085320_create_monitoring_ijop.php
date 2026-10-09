<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringIjop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_ijop', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat',10)->nullable();
            $table->integer('id_kecamatan')->nullable();
            $table->integer('id_kelurahan')->nullable();
            $table->date('tanggal');
            $table->unsignedInteger('id_petugas');
            $table->string('npsn',30);
            $table->string('hasil',30);
            $table->string('keterangan',300)->nullable();
            $table->string('path_pernyataan_tutup',300)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitoring_ijop');
    }
}
