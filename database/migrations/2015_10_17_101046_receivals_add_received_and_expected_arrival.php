<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReceivalsAddReceivedAndExpectedArrival extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivals', function(Blueprint $t) {
			$t->boolean('received');
			$t->dateTime('expected_arrival');
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
			$t->drop('received');
			$t->drop('expected_arrival');
		});
    }
}
