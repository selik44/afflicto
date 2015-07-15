<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('images', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('order');
			$table->integer('product_id')->unsigned()->index()->nullable();
			$table->integer('manufacturer_id')->unsigned()->index()->nullable();
			$table->string('type');
			$table->json('data')->nullable();

			$table->foreign('product_id')
				->references('id')->on('products')
				->onDelete('cascade');

			$table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('images');
	}

}
