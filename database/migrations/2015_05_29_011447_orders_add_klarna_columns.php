<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersAddKlarnaColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders', function(Blueprint $t) {
			$t->dropColumn('data');

			$t->string('status')        # the order status
				->default('pending');

			$t->string('reservation');

			$t->json('items');          # array of items in the cart, as it is stored in the shopping cart

			$t->float('total_price_excluding_tax');
			$t->float('total_price_including_tax');
			$t->float('total_tax_amount');

			$t->string('purchase_country');
			$t->string('purchase_currency');
			$t->string('locale');
			$t->datetime('completed_at')->nullable();

			$t->json('billing_address');
			$t->json('shipping_address');
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
			$t->string('data');
			$t->dropColumn('status');
			$t->dropColumn('items');
		});
	}

}
