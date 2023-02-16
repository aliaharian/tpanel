<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatcherTourServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watcher_tour_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_service_id')->index();
            $table->unsignedBigInteger('watcher_id')->index();

            $table->timestamps();
        });
             Schema::table('watcher_tour_services', function (Blueprint $table) {
            $table->foreign('tour_service_id')->references('id')->on('tour_services')->onDelete('cascade');
            $table->foreign('watcher_id')->references('id')->on('watchers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watcher_tour_services');
    }
}
