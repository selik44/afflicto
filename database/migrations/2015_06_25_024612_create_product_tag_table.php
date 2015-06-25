<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_tag', function(Blueprint $t) {
			$t->integer('product_id')->unsigned()->index();
			$t->integer('tag_id')->unsigned()->index();

			$t->foreign('product_id')->references('id')->on('products');
			$t->foreign('tag_id')->references('id')->on('tags');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_tag');
	}

}
