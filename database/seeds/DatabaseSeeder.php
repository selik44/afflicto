<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Friluft\User;
use Friluft\Role;
use Friluft\Store;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		/*---------------------------
		*	Roles
		*--------------------------*/
		Role::create([
			'name' => 'user',
		]);

		Role::create([
			'name' => 'admin',
		]);


		/*---------------------------
		*	Users
		*--------------------------*/
		User::create([
			'firstname' => 'Petter',
			'lastname' => 'Thowsen',
			'email' => 'me@afflicto.net',
			'password' => bcrypt(str_random(20)),
			'role_id' => Role::where('name', '=', 'admin')->first()->id,
		]);

		User::create([
			'firstname' => 'David',
			'lastname' => 'Thowsen',
			'email' => 'david@123friluft.no',
			'password' => bcrypt(str_random(20)),
			'role_id' => Role::where('name', '=', 'admin')->first()->id,
		]);

		/*---------------------------
		*	Stores
		*--------------------------*/
		Store::create([
			'machine' => 'friluft',
			'name' => '123Friluft',
			'url' => getenv('STORE_FRILUFT_URL'),
		]);

		Store::create([
			'machine' => 'highpulse',
			'name' => 'Highpulse',
			'url' => getenv('STORE_HIGHPULSE_URL'),
		]);
	}

}
