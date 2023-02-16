<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemsToHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string("early_check_in_price")->default(0);
            $table->string("late_check_out_price")->default(0);
            //free breakfast
            $table->boolean("free_breakfast_price")->default(0);
            //free lunch price
            $table->boolean("free_lunch_price")->default(0);
            //free dinner price
            $table->boolean("free_dinner_price")->default(0);
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
        Schema::table('hotels', function (Blueprint $table) {
            //
        });
    }
}
