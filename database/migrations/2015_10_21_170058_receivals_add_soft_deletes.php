<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReceivalsAddSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivals', function(Blueprint $t) {
			$t->softDeletes();
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
			$t->dropSoftDeletes();
		});
    }
}
