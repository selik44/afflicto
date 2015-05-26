<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Image extends Model {

	public $timestamps = false;
	protected $table = 'images';

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

}
