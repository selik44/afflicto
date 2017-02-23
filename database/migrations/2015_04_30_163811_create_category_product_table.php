<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category_product', function($t) {
			$t->increments('id');
			$t->integer('category_id')->unsigned();
			$t->integer('product_id')->unsigned();

			$t->foreign('category_id')
				->references('id')->on('categories')
				->onUpdate('cascade')
				->onDelete('cascade');

			$t->foreign('product_id')
				->references('id')->on('products')
				->onUpdate('cascade')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('category_product');
	}

}
