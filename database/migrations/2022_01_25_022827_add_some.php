<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSome extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profil_pokja', function (Blueprint $table) {
            $table->datetime('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::table('profil_pokja', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->dropColumn('updated_by');
            $table->dropSoftDeletes();
        });
    }
}
