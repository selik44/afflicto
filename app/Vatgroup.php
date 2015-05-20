<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Vatgroup extends Model {

	protected $table = 'vatgroups';

	protected $casts = [
		'amount' => 'float',
		'name' => 'string',
	];

	public function products() {
		return $this->hasMany('Friluft\Product');
	}

	/**
	 * Returns the VAT amount as string percentage, 0 to 100 with percentage sign.
	 */
	public function getFormattedAmount() {
		return ($this->amount - 1 ) * 100 .'%';
	}

	public function getMultiplier() {
		return 1 + ($this->amount / 100);
	}

}
