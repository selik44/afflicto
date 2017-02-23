<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersAddCouponColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $t) {
			$t->integer('coupon_id')->nullable()->unsigned();
			$t->foreign('coupon_id')->references('id')->on('coupons');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $t) {
			$t->dropForeign('coupon_id');
			$t->dropColumn('coupon_id');
		});
    }
}
