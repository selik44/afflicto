<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {

	protected $table = 'addresses';

	public $timestamps = false;

	public function user() {
		return $this->belongsTo('Friluft\User');
	}
	
}
