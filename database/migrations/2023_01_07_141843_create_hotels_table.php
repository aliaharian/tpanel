<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('stars')->default(3);
            $table->enum('type', ["hotel", "apartment", "hotelApartment"])->default('hotel');
            $table->string('address')->nullable();
            $table->unsignedBigInteger('city_id')->index();
            //latlong
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            //about
            $table->longText('description')->nullable();
            //enter and exit time
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('image_id')->nullable();
            $table->longText('notes')->nullable();
            $table->integer("capacity")->default(10);
            $table->integer("used_capacity")->default(0);
            $table->integer("available_time_from");
            $table->integer("available_time_to");
            //adult price
            $table->integer("adult_price")->default(0);
            $table->integer("teen_price")->default(0);
            $table->integer("kid_price")->default(0);
            $table->integer("infant_price")->default(0);
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
        Schema::dropIfExists('hotels');
    }
}