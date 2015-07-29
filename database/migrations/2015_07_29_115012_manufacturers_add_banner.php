<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManufacturersAddBanner extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manufacturers', function(Blueprint $t) {
			$t->integer('banner_id')->unsigned()->nullable();

			$t->foreign('banner_id')->references('id')->on('images');
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
			$t->dropForeign('manufacturers_banner_id_foreign');
			$t->removeColumn('banner');
		});
	}

}
