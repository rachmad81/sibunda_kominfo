<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJabatanPokja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('id_jabatan')->nullable();
        });

        Schema::create('jabatan_pokja', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat',20);
            $table->string('nama_jabatan',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jabatan_pokja');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_jabatan');
        });
    }
}
