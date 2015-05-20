<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Friluft\User;
use Friluft\Role;
use Friluft\Store;
use Friluft\Permission;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		# permissions
		$perms = [
			'admin.access' => 'Access Admin Backend',
			'admin.dashboard.view' => 'View Admin Dashboard',

			'products.view' => 'View Products',
			'products.edit' => 'Edit Products',
			'products.create' => 'Create Products',
			'products.delete' => 'Delete Products',

			'categories.view' => 'View Categories',
			'categories.edit' => 'Edit Categories',
			'categories.create' => 'Create Categories',
			'categories.delete' => 'Delete Categories',

			'manufacturers.view' => 'View Manufacturers',
			'manufacturers.edit' => 'Edit Manufacturers',
			'manufacturers.create' => 'Add Manufacturers',
			'manufacturers.delete' => 'Delete Manufacturers',

			'orders.view' => 'View Orders',
			'orders.edit' => 'Edit Orders',
			'orders.create' => 'Create Orders',
			'orders.delete' => 'Delete Orders',
			'orders.status.edit' => 'Edit Order Status',

			'receival.view' => 'View Receivals',
			'receival.edit' => 'Edit Receivals',
			'receival.create' => 'Create Receivals',
			'receival.delete' => 'Delete Receivals',

			'users.view' => 'View Users',
			'users.edit' => 'Edit Users',
			'users.create' => 'Create Users',
			'users.delete' => 'Delete Users',

			'store.view' => 'View Stores',
			'store.edit' => 'Edit Stores',
			'store.create' => 'Create Stores',
			'store.delete' => 'Delete Stores',

			'settings.view' => 'View Settings',
			'settings.edit' => 'Edit Settings',
		];


		foreach($perms as $machine => $name) {
			Permission::create([
				'machine' => $machine,
				'name' => $name,
			]);
		}


		/*---------------------------
		*	Roles
		*--------------------------*/
		DB::table('roles')->insert([
			[
				'name' => 'Regular',
				'machine' => 'regular',
			],
			[
				'name' => 'Administrator',
				'machine' => 'admin'
			]
		]);



		/*---------------------------
		*	Users
		*--------------------------*/
		DB::table('users')->insert([
			[
				'firstname' => 'Petter',
				'lastname' => 'Thowsen',
				'email' => 'me@afflicto.net',
				'password' => bcrypt(env('USER_PASSWORD')),
				'role_id' => Role::where('machine', '=', 'admin')->first()->id,
			],
			[
				'firstname' => 'David',
				'lastname' => 'Thowsen',
				'email' => 'david@123friluft.no',
				'password' => bcrypt(str_random(20)),
				'role_id' => Role::where('machine', '=', 'admin')->first()->id,
			]
		]);


		/*---------------------------
		*	Stores
		*--------------------------*/
		DB::table('stores')->insert([
			[
				'machine' => 'friluft',
				'name' => '123Friluft',
				'host' => getenv('STORE_FRILUFT_HOST')
			],
			[
				'machine' => 'highpulse',
				'name' => 'Highpulse',
				'host' => getenv('STORE_HIGHPULSE_HOST')
			]
		]);


		// vat groups
		DB::table('vatgroups')->insert([
			[
				'name' => '25%',
				'amount' => 1.25,
			],
			[
				'name' => 'Ingen MVA',
				'amount' => 0,
			]
		]);
	}

}
