<?php namespace Friluft\Providers;

use Illuminate\Support\ServiceProvider;
use Friluft\Utils\Datatable;

class DatatableServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        Datatable::connect("mysql:dbname=" .config('database.connections.mysql.database') .";host=" .config('database.connections.mysql.host'), config('database.connections.mysql.username'), config('database.connections.mysql.password'));
        Datatable::jQuery(false);
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
