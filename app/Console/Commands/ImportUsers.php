<?php

namespace Friluft\Console\Commands;

use DB;
use Friluft\Role;
use Illuminate\Console\Command;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mystore:importUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports users from a CSV file from mystore.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$file = base_path('resources/mystore_customers.csv');

		if ( ! file_exists($file)) {
			$this->comment($file .' does not exist.');
		}

		$users = explode("\n", file_get_contents($file));
		array_shift($users);

		$role = Role::where('machine', '=', 'regular')->first()->id;

		foreach($users as $user) {
			$csv = str_getcsv($user);
			$user = [];

			$user['id'] = $csv[0];

			$user['role_id'] = $role;

			$name = explode(',', $csv[1]);
			$user['firstname'] = trim(array_pop($name));
			$user['lastname'] = trim(array_pop($name));

			$user['billing_address'] = json_encode([
				'street_address' => trim($csv[3]),
				'city' => trim($csv[4]),
				'postal_code' => trim($csv[6]),
				'country' => trim($csv[7]),
				'phone' => trim($csv[8]),
			]);
			$user['shipping_address'] = $user['billing_address'];

			$user['phone'] = trim($csv[8]);

			$user['email'] = trim($csv[2]);


			$created = explode('/', trim($csv[9]));
			$user['created_at'] = $created[2] .'-' .$created[1] .'-' .$created[0] .' 00:00:00';

			# generate a password

			$password = str_random(16);
			$user['password'] = \Hash::make($password);
			DB::table('users')->insert($user);

			# email the user
		}

		#$user= str_getcsv();
    }
}
