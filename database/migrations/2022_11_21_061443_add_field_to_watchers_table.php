<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            //
            $table->boolean('is_haghighi')->default(1);
            $table->string('buyer_name')->nullable();
            $table->string('buyer_national_code')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('agent_phone')->nullable();
            $table->string('people_count')->nullable();



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
