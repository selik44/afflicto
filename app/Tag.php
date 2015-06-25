<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {

	public $timestamps = false;

	protected $table = 'tags';

	public function products() {
		return $this->belongsToMany('Friluft\Product');
	}

}
