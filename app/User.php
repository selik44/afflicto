<?php namespace Friluft;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * Friluft\User
 *
 * @property integer $id
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $password
 * @property integer $role_id
 * @property-read \Friluft\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Address[] $addresses
 * @property mixed $name
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereFirstname($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereLastname($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereRoleId($value)
 * @property string $phone
 * @property string $billing_address
 * @property string $shipping_address
 * @property-read \Friluft\Address $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Coupon[] $coupons
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereBillingAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\User whereShippingAddress($value)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, SoftDeletes;

	public $timestamps = true;

	protected $table = 'users';

	protected $fillable = ['firstname', 'lastname', 'email'];

	protected $hidden = ['password', 'remember_token'];

	protected $casts = [
		'shipping_address' => 'array',
		'billing_address' => 'array',
	];

	public function role() {
		return $this->belongsTo('Friluft\Role');
	}

	public function orders() {
		return $this->hasMany('Friluft\Order');
	}

	public function address() {
		return $this->hasOne('Friluft\Address');
	}

	public function getNameAttribute() {
		return $this->attributes['firstname'] .' ' .$this->attributes['lastname'];
	}

	public function setNameAttribute($value) {
		$name = explode(' ', $value, 1);
		if (count($name) > 1) {
			$this->attributes['firstname'] = $name[0];
			$this->attributes['lastname'] = $name[1];
		}
	}

	/**
	 * Get the coupons this user has used.
	 */
	public function coupons() {
		return $this->belongsToMany('Friluft\Coupon');
	}

}