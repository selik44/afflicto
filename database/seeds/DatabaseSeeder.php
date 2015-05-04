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
		DB::delete('delete from stores');
		DB::delete('delete from roles');
		DB::delete('delete from users');

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
			'password' => bcrypt('kake'),
			'role_id' => 2,
		]);

		User::create([
			'firstname' => 'John',
			'lastname' => 'Doe',
			'email' => 'john@johndoe.com',
			'role_id' => 1,
			'password' => bcrypt('kake'),
		]);


		/*---------------------------
		*	Stores
		*--------------------------*/
		Store::create([
			'machine' => 'friluft',
			'name' => '123Friluft',
			'url' => '123friluft.dev',
		]);

		Store::create([
			'machine' => 'highpulse',
			'name' => 'Highpulse',
			'url' => 'highpulse.dev',
		]);
	}

}
