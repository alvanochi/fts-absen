<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPembelajaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembelajaran', function (Blueprint $table) {
            $table->text('rps_dasar')->nullable();
            $table->text('rps_pelaksanaan')->nullable();
            $table->string('nidn_dosen_pengganti')->nullable();
            $table->string('dosen_tamu')->nullable();
            $table->string('npm_komti')->nullable();
            $table->string('name_komti')->nullable();
            $table->string('learning_done')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembelajaran', function (Blueprint $table) {
            $table->dropColumn([
                'rps_dasar',
                'rps_pelaksanaan',
                'nidn_dosen_pengganti',
                'dosen_tamu',
                'npm_komti',
                'name_komti',
                'learning_done'
            ]);
        });
    }
}
