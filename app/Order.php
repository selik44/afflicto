<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

	protected $table = 'orders';

	protected $dates = ['created_at', 'updated_at', 'completed_at'];
	
	protected $casts = [
		'items' => 'array',
		'total_price_excluding_tax' => 'float',
		'total_price_including_tax' => 'float',
		'total_tax_amount' => 'float',
		'billing_address' => 'array',
		'shipping_address' => 'array',
	];

	public function user() {
		return $this->belongsTo('Friluft\User');
	}
	
}
