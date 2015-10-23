<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReceivalsAddReceivalId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivals', function(Blueprint $t) {
			$t->integer('receival_id')->unsigned()->index()->nullable();
			$t->foreign('receival_id')->references('id')->on('receivals');
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
			$t->dropForeign('receival_id');
			$t->dropColumn('receival_id');
		});
    }
}
