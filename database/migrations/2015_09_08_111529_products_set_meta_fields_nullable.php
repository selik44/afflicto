<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsSetMetaFieldsNullable extends Migration
{
	public function up()
	{
		Schema::table('products', function(Blueprint $t) {
			$t->string('meta_description', 60)->nullable()->change();
			$t->text('meta_keywords')->nullable()->change();
		});
	}

	public function down()
	{
		Schema::table('products', function(Blueprint $t) {
			$t->string('meta_description', 60)->change();
			$t->string('meta_keywords')->change();
		});
	}
}
