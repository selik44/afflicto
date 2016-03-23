<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRelationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_relation', function(Blueprint $t) {
			$t->increments('id');
			$t->integer('product_id')->unsigned()->index();
			$t->integer('relation_id')->unsigned()->index();

			$t->foreign('product_id')
				->references('id')->on('products')
				->onDelete('cascade');
			$t->foreign('relation_id')
				->references('id')->on('products')
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
		Schema::drop('product_relation');
	}

}
