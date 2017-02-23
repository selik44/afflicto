<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->rememberToken();
			$table->timestamps();
			$table->softDeletes();

			$table->string('firstname');
			$table->string('lastname');
			$table->string('email')->unique();
			$table->string('password', 60);

			$table->integer('role_id')->unsigned()->index()->nullable();
			$table->foreign('role_id')->references('id')->on('roles');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
