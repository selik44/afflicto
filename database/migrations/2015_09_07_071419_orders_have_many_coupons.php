<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersHaveManyCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('coupon_order', function(Blueprint $t) {
			$t->increments('id');
			$t->integer('coupon_id')->unsigned();
			$t->integer('order_id')->unsigned();

			$t->foreign('coupon_id')->references('id')->on('coupons')->onUpdate('cascade');
			$t->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_order');
    }
}
