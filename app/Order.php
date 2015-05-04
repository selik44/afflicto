<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

	protected $table = 'orders';

	protected $casts = [
		'data' => 'array',
	];

	public function user() {
		return $this->belongsTo('Friluft\User');
	}
	
}
