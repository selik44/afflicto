<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReceivalsAddRestBoolean extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('receivals', function(Blueprint $t) {
			$t->boolean('rest');
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
			$t->dropColumn('rest');
		});
    }
}
