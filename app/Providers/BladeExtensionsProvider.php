<?php namespace Friluft\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;

class BladeExtensionsProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Blade::extend(function($view, $compiler) {
			$pattern = $compiler->createMatcher('inject');
			
			return preg_replace($pattern, '', $view);
		});
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
