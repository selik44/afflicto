<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model {

	public $timestamps = false;
	protected $table = 'manufacturers';

	protected $fillable = ['name','slug'];


	public function products() {
		$this->hasMany('Friluft\Product');
	}

}
