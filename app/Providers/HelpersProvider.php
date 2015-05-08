<?php namespace Friluft\Providers;

use Illuminate\Support\ServiceProvider;

class HelpersProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		require_once app_path() .'/helpers.php';
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
