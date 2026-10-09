<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password',100);
            $table->string('role',50);
            $table->string('tingkat',50);
            $table->unsignedInteger('id_kecamatan')->nullable();
            $table->unsignedInteger('id_kelurahan')->nullable();
            $table->string('nama',250);
            $table->string('tempat_lahir',50)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('kelamin',2)->nullable();
            $table->string('no_telp',20)->nullable();
            $table->string('alamat',500)->nullable();
            $table->string('path_foto',250)->nullable();
            $table->string('no_sk_penetapan',250)->nullable();
            $table->date('tgl_sk_penetapan')->nullable();
            $table->string('path_sk_penetapan',250)->nullable();
            $table->string('pendidikan_terakhir',10)->nullable();
            $table->string('jurusan_pendidikan',150)->nullable();
            $table->text('kata_sambutan')->nullable();
            $table->boolean('verified')->default(false);
            $table->datetime('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
