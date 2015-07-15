<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCategoriesTo1to1Relationship extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('category_product');

		Schema::table('products', function($t) {
			$t->integer('category_id')->unsigned()->nullable();
			$t->foreign('category_id')->references('id')->on('categories')->onDelete('SET NULL');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('category_product', function($t) {
			$t->increments('id');

			$t->integer('category_id')->unsigned();
			$t->integer('product_id')->unsigned();

			$t->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
			$t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
		});
	}

}
