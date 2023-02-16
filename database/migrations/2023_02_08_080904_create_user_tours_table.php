<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_city_id')->index();
            $table->unsignedBigInteger('to_city_id')->index();
            $table->unsignedBigInteger('departure_date_time');
            $table->unsignedBigInteger('arrival_date_time');
            $table->unsignedBigInteger('adult_count');
            $table->unsignedBigInteger('teen_count');
            $table->unsignedBigInteger('kid_count');
            $table->unsignedBigInteger('infant_count');
            $table->unsignedBigInteger('hotel_id')->index();
            $table->unsignedBigInteger('departure_vehicle_id')->index();
            $table->unsignedBigInteger('arrival_vehicle_id')->index();
            $table->unsignedBigInteger('agency_id')->index()->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('rooms_count')->nullable();
            $table->unsignedBigInteger('rooms_name')->nullable();
            $table->boolean('fullboard')->default(false);
            $table->unsignedBigInteger('status_id')->index();
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
            $table->boolean('payed')->default(false);
            $table->unsignedBigInteger('payablePrice')->nullable();
            $table->timestamps();

            $table->foreign('from_city_id')->references('id')->on('province_cities')->onDelete('cascade');
            $table->foreign('to_city_id')->references('id')->on('province_cities')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('departure_vehicle_id')->references('id')->on('transport_vehicles')->onDelete('cascade');
            $table->foreign('arrival_vehicle_id')->references('id')->on('transport_vehicles')->onDelete('cascade');
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('tour_statuses')->onDelete('cascade');
            // $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tours');
    }
}