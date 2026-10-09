<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program', function (Blueprint $table) {
            $table->increments('id_program');
            $table->string('waktu',100)->nullable();
            $table->string('nama_kegiatan',255)->nullable();
            $table->string('sasaran',255)->nullable();
            $table->string('tujuan',255)->nullable();
            $table->string('tahun',10)->nullable();
            $table->string('updated_by',100)->nullable();
            $table->datetime('updated_at')->nullable();
            $table->integer('id_kecamatan')->nullable();
            $table->integer('id_kelurahan')->nullable();
            $table->string('tingkat',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('program');
    }
}
