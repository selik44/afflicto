<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Receival
 *
 * @property integer $id
 * @property string $products
 * @property string $when
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Receival whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Receival whereProducts($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Receival whereWhen($value)
 */
class Receival extends Model {

	public $timestamps = false;

	protected $table = 'receivals';

	protected $casts = [
		'products' => 'array',
		'received' => 'boolean',
	];

	protected $dates = ['expected_arrival'];

	public function manufacturer() {
		return $this->belongsTo('Friluft\Manufacturer');
	}

}
