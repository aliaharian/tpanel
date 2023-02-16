<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteSomeFieldsFromWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            //delete departure_transport_name field
            $table->dropColumn('departure_transport_name');
            // delete departure_transport_logo
            $table->dropColumn('departure_transport_logo');
            //delete departure_date
            $table->dropColumn('departure_date');
            //delete departure_time
            $table->dropColumn('departure_time');
            //delete arrival_transport_name
            $table->dropColumn('arrival_transport_name');
            //delete arrival_transport_logo
            $table->dropColumn('arrival_transport_logo');
            //delete arrival_date
            $table->dropColumn('arrival_date');
            //delete arrival_time
            $table->dropColumn('arrival_time');
            
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
        Schema::table('watchers', function (Blueprint $table) {
            //
        });
    }
}
