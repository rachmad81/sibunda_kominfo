<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApresiasiKomponenTeknik extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apresiasi_komponen_teknik', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('komponen',500);
            $table->integer('bobot');
        });

        Schema::create('apresiasi_indikator_skor', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('indikator',100);
            $table->integer('skor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apresiasi_komponen_teknik');
        Schema::dropIfExists('apresiasi_indikator_skor');
    }
}
