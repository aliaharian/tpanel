<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferFieldsToWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->unsignedBigInteger('departure_vehicle_id')->nullable();
            $table->unsignedBigInteger('arrival_vehicle_id')->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watchers', function (Blueprint $table) {
            //
        });
    }
}
