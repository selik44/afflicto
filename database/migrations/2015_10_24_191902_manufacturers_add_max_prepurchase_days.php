<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManufacturersAddMaxPrepurchaseDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturers', function(Blueprint $t) {
			$t->dropColumn('always_allow_orders');
			$t->boolean('prepurchase_enabled');
			$t->integer('prepurchase_days');
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
			$t->dropColumn('prepurchase_enabled');
			$t->dropColumn('prepurchase_days');
			$t->boolean('always_allow_orders');
		});
    }
}
