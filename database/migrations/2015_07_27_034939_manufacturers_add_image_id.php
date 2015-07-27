<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManufacturersAddImageId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manufacturers', function(Blueprint $t) {
			$t->integer('image_id')->unsigned()->nullable();

			$t->foreign('image_id')->references('id')->on('images')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manufacturers', function(Blueprint $t) {
			$t->dropForeign('manufacturers_image_id_foreign');
			$t->dropColumn('image_id');
		});
	}

}
