<?php namespace Friluft\Providers;

use Illuminate\Support\ServiceProvider;
use Friluft\Shopping\Cart\Cart;

class ShoppingCartProvider extends ServiceProvider {

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
		$this->app->singleton('cart', function($app) {
			return new Cart($app['session']);
		});
	}

}
