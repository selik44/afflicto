<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReceivalsAddManfuacturerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivals', function(Blueprint $t) {
			$t->integer('manufacturer_id')->unsigned();

			$t->foreign('manufacturer_id')->references('id')->on('manufacturers');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivals', function(Blueprint $t) {
			$t->dropForeign('manufacturer_id');

			$t->drop('manfuacturer_id');
		});
    }
}
