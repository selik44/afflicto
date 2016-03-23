<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsChangeArticlenumberNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('products', function(Blueprint $t) {
			$t->integer('articlenumber')->unsigned()->nullable()->change();
			$t->string('barcode', 13)->nullable()->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('products', function(Blueprint $t) {
			$t->integer('articlenumber')->unsigned()->change();
			$t->string('barcode', 13)->change();
		});
    }
}
