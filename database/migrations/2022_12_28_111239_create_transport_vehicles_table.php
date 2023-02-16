<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_city')->index();
            $table->unsignedBigInteger('to_city')->index();
            $table->unsignedBigInteger('transport_company_id');
            $table->string('departure_date_time');
            $table->string('arrival_date_time');
            $table->enum('transport_type', ['BUS', 'TRAIN', 'AIRPLANE']);
            $table->string('transport_number')->nullable();
            $table->string('transport_class')->nullable();
            $table->unsignedBigInteger('capacity');
            $table->string('adult_price');
            $table->string('teen_price');
            $table->string('kid_price');
            $table->string('infant_price');
            $table->json('meta')->nullable();
            $table->boolean('active')->default(1);

            $table->timestamps(); 
        });

        Schema::table('transport_vehicles', function (Blueprint $table) {
            $table->foreign('from_city')->references('id')->on('province_cities')->onDelete('cascade');
            $table->foreign('to_city')->references('id')->on('province_cities')->onDelete('cascade');
            $table->foreign('transport_company_id')->references('id')->on('transport_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_vehicles');
    }
}
