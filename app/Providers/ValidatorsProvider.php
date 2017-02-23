<?php namespace Friluft\Providers;

use Illuminate\Support\ServiceProvider;

class ValidatorsProvider extends ServiceProvider {

	public function boot()
	{
		$validator = $this->app['validator'];

		$validator->extend('slug', function($attribute, $value) {
			return preg_match('/^([a-z]+-?)+([a-z])$/', $value);
		});

		$validator->extend('machine', function($attribute, $value) {
			return preg_match('/^([a-z]+_?)+([a-z])$/', $value);
		});
	}

	public function register()
	{

	}

}
