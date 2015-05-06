<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $t)
		{
			$t->increments('id');
			$t->timestamps();
			$t->string('name');
			$t->string('brand');
			$t->string('model');
			$t->float('weight');
			$t->text('description');
			$t->float('price');
			$t->float('in_price');
			$t->float('tax_percentage');
			$t->integer('stock');
			$t->string('slug');
			$t->text('images');
			$t->boolean('enabled')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
