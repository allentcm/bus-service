<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBusStopIdToBusStopCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->renameColumn('bus_stop_id', 'bus_stop_code');
            $table->string('bus_stop_code')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->renameColumn('bus_stop_code', 'bus_stop_id');
            $table->integer('bus_stop_id')->unsigned()->nullable()->change();
        });
    }
}
