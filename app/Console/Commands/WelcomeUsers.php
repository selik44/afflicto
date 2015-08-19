<?php

namespace Friluft\Console\Commands;

use Friluft\Store;
use Friluft\User;
use Hash;
use Illuminate\Console\Command;

class WelcomeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mystore:welcomeUsers {store=friluft}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a welcome message to all users whose password is set to "nothing".';

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

		$store = Store::whereMachine($this->argument('store'))->first();
		Store::setCurrentStore($store);
		
        $users = User::where('password', '=', 'nothing')->get();
		foreach($users as $user) {
			# generate password
			$password = str_random(12);

			# store a hash of it
			$user->password = Hash::make($password);

			# save the user
			#$user->save();

			# get email address
			$email = $user->email;

			\Mail::send('emails.store.transition', ['password' => $password], function($send) use($email) {
				$send->to('me@afflicto.net')->subject('Velkommen til en helt ny 123friluft.no!');
			});
			return true;
		}
    }
}
