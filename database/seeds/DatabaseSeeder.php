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
				'machine' => 'superadmin'
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
				'role_id' => Role::where('machine', '=', 'superadmin')->first()->id,
			],
			[
				'firstname' => 'David',
				'lastname' => 'Thowsen',
				'email' => 'david@123friluft.no',
				'password' => bcrypt(str_random(20)),
				'role_id' => Role::where('machine', '=', 'superadmin')->first()->id,
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

        // manufacturers
        DB::table('manufacturers')->insert([
            'name' => 'HighPulse',
            'slug' => 'highpulse',
        ]);

		// settings
		DB::table('settings')->insert([
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'slogan_content',
				'value' => 'FRI FRAKT: HANDLE FOR OVER 800,- OG FÅ GRATIS FRAKT.',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'color',
				'machine' => 'slogan_color',
				'value' => '#ffffff',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'color',
				'machine' => 'slogan_background',
				'value' => '#01a1a5',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'footer_1_content',
				'value' => 'Footer 1',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'footer_2_content',
				'value' => 'Footer 2',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'footer_3_content',
				'value' => 'Footer 3',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'checkout_1_content',
				'value' => 'Checkout 1',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'checkout_2_content',
				'value' => 'Checkout 2',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'checkout_3_content',
				'value' => 'Checkout 3',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'store_slogan_1_content',
				'value' => 'Slogan 1',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'store_slogan_2_content',
				'value' => 'Slogan 2',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'store_slogan_3_content',
				'value' => 'Slogan 3',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'General',
				'type' => 'html',
				'machine' => 'store_slogan_4_content',
				'value' => 'Slogan 4',
				'name' => '',
				'description' => null,
			],
			[
				'category' => 'SEO',
				'type' => 'text',
				'machine' => 'meta_description',
				'name' => 'Meta Description',
				'value' => 'meta description here',
				'description' => null,
			],
			[
				'category' => 'SEO',
				'type' => 'text',
				'machine' => 'meta_keywords',
				'name' => 'Metea Keywords',
				'value' => 'meta,keywords,here',
				'description' => null,
			],
		]);

		// Tags
		DB::table('tags')->insert([
			[
				'label' => 'Popular',
				'icon' => '',
				'color' => '#000000',
				'enabled' => 1,
				'type' => 'popular',
				'visible' => 0,
				'discount' => 0,
			],
			[
				'label' => 'Gratis Frakt!',
				'fa fa-check',
				'color' => '#ff9300',
				'enabled' => 1,
				'type' => 'free_shiping',
				'visible' => 0,
				'discount' => 0,
			],
			[
				'label' => 'DAGFSKUPP!',
				'icon' => 'fa fa-shopping-cart',
				'color' => '#00fdff',
				'enabled' => 1,
				'type' => null,
				'visible' => 1,
				'discount' => 0,
			],
			[
				'label' => 'Checkout',
				'icon' => '',
				'color' => '#000000',
				'enabled' => 1,
				'type' => 'checkout',
				'visible' => 0,
				'discount' => 0,
			],
			[
				'label' => 'Nyhet',
				'icon' => '',
				'color' => '#000000',
				'enabled' => 1,
				'type' => 'news',
				'visible' => 0,
				'discount' => 0,
			],
		]);
	}

}
