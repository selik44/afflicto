<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Store
 *
 * @property integer $id
 * @property string $machine
 * @property string $name
 * @property string $host
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereMachine($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Store whereHost($value)
 */
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