<?php

use Friluft\Variant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VariantsManyToMany extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		# delete all variants
		DB::table('variants')->delete();

		# create variant_product pivot table
		Schema::create('product_variant', function(Blueprint $t) {
			$t->increments('id');
			$t->integer('variant_id')->unsigned()->index();
			$t->integer('product_id')->unsigned()->index();
			$t->json('data');

			$t->foreign('variant_id')->references('id')->on('variants')->onDelete('CASCADE');
			$t->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
		});

		# remove the variant_id from the variants table
		Schema::table('variants', function(Blueprint $t) {
			$t->dropForeign('variants_product_id_foreign');
			$t->dropIndex('variants_product_id_index');
			$t->dropColumn('product_id');
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

		Schema::drop('variant_product');

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}
