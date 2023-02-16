<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watchers', function (Blueprint $table) {
            $table->id();
            $table->enum('departure_transport_type',['BUS','TRAIN','AIRPLANE']);
            $table->string('departure_transport_name');
            $table->longText('departure_transport_logo');
            $table->date('departure_date');
            $table->string('departure_time');

            $table->enum('arrival_transport_type',['BUS','TRAIN','AIRPLANE']);
            $table->string('arrival_transport_name');
            $table->longText('arrival_transport_logo');
            $table->date('arrival_date');
            $table->string('arrival_time');
            
            $table->string('hotel_name');
            $table->integer('room_numbers');
            $table->integer('room_type');
            $table->boolean('breakfast')->default(1);
            $table->boolean('fullboard')->default(0);
            $table->integer('stay_length');

            $table->unsignedBigInteger('price_per_adult');
            $table->unsignedBigInteger('total_price');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watchers');
    }
}
