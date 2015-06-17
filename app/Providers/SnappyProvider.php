<?php namespace Friluft\Providers;

use Friluft\PDF\Snappy;
use Illuminate\Support\ServiceProvider;

class SnappyProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('snappy', function($app) {
			return new Snappy(base_path('vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386'));
		});
	}

}
