<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManufacturersAddAlwaysAllowOrdersColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manufacturers', function(Blueprint $t) {
			$t->boolean('always_allow_orders')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manufacturers', function(Blueprint $t) {
			$t->dropColumn('always_allow_orders');
		});
	}

}
