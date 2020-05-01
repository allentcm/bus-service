<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCoordinatePrecision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE bus_stops CHANGE COLUMN latitude latitude DOUBLE(20,16) NULL DEFAULT NULL ;');
        DB::statement('ALTER TABLE bus_stops CHANGE COLUMN longitude longitude DOUBLE(20,16) NULL DEFAULT NULL ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bus_stops', function (Blueprint $table) {
            //
        });
    }
}
