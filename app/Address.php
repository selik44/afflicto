<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Address
 *
 * @property integer $id
 * @property string $country
 * @property string $county
 * @property string $street
 * @property string $postcode
 * @property integer $user_id
 * @property-read \Friluft\User $user
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereCounty($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereStreet($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address wherePostcode($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Address whereUserId($value)
 * @mixin \Eloquent
 */
class Address extends Model {

	protected $table = 'addresses';

	public $timestamps = false;

	public function user() {
		return $this->belongsTo('Friluft\User');
	}
	
}
