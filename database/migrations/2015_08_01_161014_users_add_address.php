<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddAddress extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $t) {
			$t->string('phone')->nullable();
			$t->json('billing_address')->nullable();
			$t->json('shipping_address')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $t) {
			$t->dropColumn(['phone', 'billing_address', 'shipping_address']);
		});
	}

}
