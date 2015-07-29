<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CategoriesAddBannerIdColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('categories', function($t) {
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
		Schema::table('categories', function($t) {
			$t->dropForeign('categories_banner_id_foreign');
			$t->dropColumn('banner_id');
		});
	}

}
