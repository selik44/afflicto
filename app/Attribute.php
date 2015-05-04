<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model {

	protected $table = 'attributes';

	public function fields() {
		return $this->hasMany('Friluft\Field');
	}

	public function products() {
		return $this->belongsToMany('Friluft\Product');
	}

}
