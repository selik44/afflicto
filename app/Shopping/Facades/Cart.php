<?php namespace Friluft\Shopping\Facades;

class Cart extends \Illuminate\Support\Facades\Facade {

	public static function getFacadeAccessor()
	{
		return 'cart';
	}

}