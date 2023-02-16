<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMealsToUserToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tours', function (Blueprint $table) {
            $table->boolean('breakfast')->default(false)->after('fullboard');
            $table->boolean('lunch')->default(false)->after('breakfast');
            $table->boolean('dinner')->default(false)->after('lunch');
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
        Schema::table('user_tours', function (Blueprint $table) {
            //
        });
    }
}
