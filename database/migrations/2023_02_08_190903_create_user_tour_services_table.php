<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTourServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tour_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_tour_id')->index();
            $table->unsignedBigInteger('tour_service_id')->index();
            $table->timestamps();

            $table->foreign('user_tour_id')->references('id')->on('user_tours')->onDelete('cascade');
            $table->foreign('tour_service_id')->references('id')->on('tour_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tour_services');
    }
}
