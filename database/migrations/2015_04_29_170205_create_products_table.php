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
			$t->softDeletes();
			$t->string('name', 50);
			$t->string('slug', 50);
			$t->integer('price');
			$t->integer('articlenumber')->unsigned();
			$t->string('barcode', 13)->nullable();
			$t->integer('inprice');
			$t->integer('weight');
			$t->text('description');
			$t->text('summary');
			$t->integer('stock');//only used for products that don't have any variants
			$t->boolean('enabled')->default(1);
			$t->integer('sales')->unsigned();
			$t->integer('vatgroup_id')->unsigned();
			$t->integer('manufacturer_id')->unsigned()->nullable();

			$t->string('categories')->default('');

			$t->foreign('vatgroup_id')
				->references('id')->on('vatgroups');

			$t->foreign('manufacturer_id')
				->references('id')->on('manufacturers');
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
