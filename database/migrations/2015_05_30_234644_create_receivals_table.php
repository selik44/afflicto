<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivalsTable extends Migration {

	private $permissions = [
		'receivals.view' => 'View Receivals',
		'receivals.edit' => 'Edit Receivals',
		'receivals.create' => 'Create Receivals',
		'receivals.delete' => 'Delete Receivals',
	];

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('receivals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->json('products');
			$table->datetime('when');
		});

		# add permissions
		if (Schema::hasTable('permissions')) {
			foreach($this->permissions as $machine => $name) {
				DB::table('permissions')->insert([
					'machine' => $machine,
					'name' => $name,
				]);
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('receivals');

		# drop permissions
		if (Schema::hasTable('permissions')) {
			foreach($this->permissions as $machine => $name) {
				DB::table('permissions')->where('machine', '=', $machine)->delete();
			}
		}
	}

}
