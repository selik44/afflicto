<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

	protected $table = 'orders';

	protected $dates = ['created_at', 'updated_at'];
	
	protected $casts = [
		'data' => 'array',
	];

	public function user() {
		return $this->belongsTo('Friluft\User');
	}
	
}
