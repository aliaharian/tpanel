<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('slug');
            $table->string('color');
            $table->timestamps();

            //seed
            // $table->insert([
            //     [
            //         'value' => 'خرید بلیت',
            //         'slug' => 'BUY_TICKET',
            //         'color' => '#20CD85',
            //         'created_at' => now(),
            //         'updated_at' => now()
            //     ],
            //     [
            //         'value' => 'شارژ حساب',
            //         'slug' => 'CHARGE_ACCOUNT',
            //         'color' => '#406D97',
            //         'created_at' => now(),
            //         'updated_at' => now()
            //     ],
            //     [
            //         'value' => 'برداشت وجه',
            //         'slug' => 'WITHDRAW_MONEY',
            //         'color' => '#FF5A5F',
            //         'created_at' => now(),
            //         'updated_at' => now()
            //     ],
            // ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_types');
    }
}