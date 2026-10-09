<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSkFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pejabat_sk_penetapan');
            $table->dropColumn('no_sk_penetapan');
            $table->dropColumn('tgl_sk_penetapan');
            $table->dropColumn('path_sk_penetapan');
            $table->dropColumn('email');
            $table->dropColumn('no_telp');
            $table->dropColumn('alamat');
            $table->dropColumn('tempat_lahir');
            $table->dropColumn('tgl_lahir');
            $table->dropColumn('kelamin');
            $table->dropColumn('agama');
            $table->dropColumn('path_foto');
            $table->dropColumn('pendidikan_terakhir');
            $table->dropColumn('jurusan_pendidikan');
            $table->dropColumn('kata_sambutan');
        });

        Schema::create('berkas_pokja', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tingkat',15);
            $table->integer('id_kecamatan')->nullable();
            $table->integer('id_kelurahan')->nullable();
            $table->string('no_sk_penetapan_bunda',250);
            $table->string('path_sk_penetapan_bunda',250);
            $table->date('tgl_sk_penetapan_bunda');
            $table->string('pejabat_sk_penetapan_bunda',250);
            $table->string('no_sk_penetapan_pokja',250);
            $table->string('path_sk_penetapan_pokja',250);
            $table->date('tgl_sk_penetapan_pokja');
            $table->string('pejabat_sk_penetapan_pokja',250);
            $table->datetime('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
        });

        Schema::create('profil_pokja', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tingkat',15);
            $table->integer('id_kecamatan')->nullable();
            $table->integer('id_kelurahan')->nullable();
            $table->unsignedInteger('id_jabatan')->nullable();
            $table->string('nama',250)->nullable();
            $table->string('email')->nullable();
            $table->string('agama')->nullable();
            $table->string('tempat_lahir',50)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('kelamin',2)->nullable();
            $table->string('no_telp',20)->nullable();
            $table->string('alamat',500)->nullable();
            $table->string('path_foto',250)->nullable();
            $table->string('pendidikan_terakhir',10)->nullable();
            $table->string('jurusan_pendidikan',150)->nullable();
            $table->text('kata_sambutan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('tempat_lahir',50)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('email',150)->nullable();
            $table->string('agama',15)->nullable();
            $table->string('kelamin',2)->nullable();
            $table->string('no_telp',20)->nullable();
            $table->string('alamat',500)->nullable();
            $table->string('path_foto',250)->nullable();
            $table->string('no_sk_penetapan',250)->nullable();
            $table->string('path_sk_penetapan',250)->nullable();
            $table->date('tgl_sk_penetapan')->nullable();
            $table->string('pejabat_sk_penetapan',250)->nullable();
            $table->string('pendidikan_terakhir',10)->nullable();
            $table->string('jurusan_pendidikan',150)->nullable();
            $table->text('kata_sambutan')->nullable();
        });
        Schema::dropIfExists('berkas_pokja');
        Schema::dropIfExists('profil_pokja');
    }
}
