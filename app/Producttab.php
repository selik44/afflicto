<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Producttab extends Model {

	public $timestamps = false;

	protected $table = 'producttabs';

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

}
