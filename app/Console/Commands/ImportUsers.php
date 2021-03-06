<?php

namespace Friluft\Console\Commands;

use DB;
use ForceUTF8\Encoding;
use Friluft\Role;
use Friluft\User;
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

		$str = file_get_contents($file);
		$str = Encoding::toUTF8($str);

		$users = explode("\n", $str);
		array_shift($users);

		$role = Role::where('machine', '=', 'regular')->first()->id;

		foreach($users as $user) {
			$csv = array_merge(str_getcsv($user), [
				'','','','','','','','',''
			]);

			# no email?
			if (strlen(trim($csv[2])) == 0) {
				$this->comment('empty email address, skipping.');
				continue;
			}

			# already exists?
			if (User::where('email', '=', $csv[2])->count() > 0) {
				$this->comment('skipping user, email already exists: ' .$csv[2]);
				continue;
			}

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
			if (isset($created[2]) && isset($created[1]) && isset($created[0])) {
				$user['created_at'] = $created[2] .'-' .$created[1] .'-' .$created[0] .' 00:00:00';
			}

			# generate a password
			$user['password'] = 'nothing';
			DB::table('users')->insert($user);
		}
    }
}
