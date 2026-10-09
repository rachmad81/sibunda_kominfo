<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->increments('id_kegiatan');
            $table->integer('program_id');
            $table->date('tgl_kegiatan')->nullable();
            $table->string('nama_kegiatan',150)->nullable();
            $table->string('bentuk_keg',150)->nullable();
            $table->text('materi')->nullable();
            $table->string('narsum',150)->nullable();
            $table->string('sumber_dana',150)->nullable();
            $table->string('foto',255)->nullable();
            $table->string('url_video',255)->nullable();
            $table->string('updated_by',255)->nullable();
            $table->datetime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kegiatan');
    }
}
