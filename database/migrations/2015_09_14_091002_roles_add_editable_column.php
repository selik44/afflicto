<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RolesAddEditableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function(Blueprint $t) {
			$t->boolean('editable')->default(1);
		});

		DB::table('roles')->where('machine', '=', 'superadmin')->update(['editable' => '0']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('roles', function(Blueprint $t) {
			$t->dropColumn('editable');
		});
    }
}
