<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('agency_name');
            $table->string('agency_logo')->nullable();
            $table->string('agency_off_percent')->default(0);
            $table->string('agency_markup_percent')->default(0);
            $table->boolean('status')->default(1);
          
            $table->timestamps();
        });
        // Schema::table('agencies', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agencies');
    }
}
