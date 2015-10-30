<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSizemapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sizemaps', function(Blueprint $t) {
			$t->increments('id');
			$t->string('name');
			$t->string('image');
		});

		Schema::table('products', function(Blueprint $t) {
			$t->integer('sizemap_id')->nullable()->unsigned()->index();

			$t->foreign('sizemap_id')->references('id')->on('sizemaps');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sizemaps');

		Schema::table('products', function(Blueprint $t) {
			$t->dropForeign('sizemap_id');
			$t->drop('sizemap_id');
		});
    }
}
