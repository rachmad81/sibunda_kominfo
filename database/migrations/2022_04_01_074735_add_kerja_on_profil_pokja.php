<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKerjaOnProfilPokja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profil_pokja', function (Blueprint $table) {
            $table->string('profesi',100)->nullable();
            $table->string('unit_kerja',100)->nullable();
            $table->string('alamat_kerja',200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profil_pokja', function (Blueprint $table) {
            $table->dropColumn('profesi');
            $table->dropColumn('unit_kerja');
            $table->dropColumn('alamat_kerja');
        });
    }
}
