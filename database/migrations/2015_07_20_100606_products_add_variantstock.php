<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsAddVariantstock extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		Schema::table('products', function($t) {
			$t->json('variants_stock')->nullable();
		});

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		Schema::table('products', function($t) {
			$t->json('variants_stock')->nullable();
		});

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}
