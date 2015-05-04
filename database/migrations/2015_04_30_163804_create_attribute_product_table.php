<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attribute_product', function($t) {
			$t->increments('id');
			$t->integer('attribute_id')->unsigned();
			$t->integer('product_id')->unsigned();

			$t->foreign('attribute_id')->references('id')->on('attributes');
			$t->foreign('product_id')->references('id')->on('products');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attribute_product');
	}

}
