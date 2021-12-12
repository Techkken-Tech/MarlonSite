<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGcashSourceIdToCartPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_payment', function (Blueprint $table) {
            $table->string('temp_source')->nullable();
            $table->string('gcash_source_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_payment', function (Blueprint $table) {
            $table->string('temp_source')->nullable();
            $table->string('gcash_source_id')->nullable();
        });
    }
}
