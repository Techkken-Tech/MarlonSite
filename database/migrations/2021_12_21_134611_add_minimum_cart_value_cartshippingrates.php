<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimumCartValueCartshippingrates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_shipping_rates', function (Blueprint $table) {
            $table->float('minimum_cartvalue', 12, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_shipping_rates', function (Blueprint $table) {
            $table->dropColumn('minimum_cartvalue');
        });
    }
}
