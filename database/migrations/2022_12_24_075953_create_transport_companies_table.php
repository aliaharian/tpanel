<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('logo_id')->index()->nullable();
            $table->enum('transport_type', ['BUS', 'TRAIN', 'AIRPLANE']);
            $table->boolean('active');
            $table->timestamps();
        });
        Schema::table('transport_companies', function (Blueprint $table) {
            $table->foreign('logo_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_companies');
    }
}