<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permission_role', function(Blueprint $t) {
			$t->increments('id');
			$t->integer('permission_id')->unsigned()->index();
			$t->integer('role_id')->unsigned()->index();

			$t->foreign('permission_id')
				->references('id')->on('permissions')
				->onDelete('cascade');

			$t->foreign('role_id')
				->references('id')->on('roles')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permission_role');
	}

}
