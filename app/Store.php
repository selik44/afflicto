<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Store extends Model {

	protected $table = 'stores';

	public $timestamps = false;

	private static $currentStore = null;

	public static function setCurrentStore(Store $store) {
		static::$currentStore = $store;
	}

	public static function current() {
		return static::$currentStore;
	}

}