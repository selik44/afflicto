<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Taxgroup extends Model {

	public $timestamps = false;
	protected $table = 'taxgroups';

	public function products() {
		return $this->hasMany('Friluft\Product');
	}

}
