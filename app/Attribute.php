<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Attribute
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Field[] $fields
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @mixin \Eloquent
 */
class Attribute extends Model {

	protected $table = 'attributes';

	// public function fields() {
	// 	return $this->hasMany('Friluft\Field');
	// }

	public function products() {
		return $this->belongsToMany('Friluft\Product');
	}

}