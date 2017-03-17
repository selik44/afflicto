<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Friluft\Coupon
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $admin_name
 * @property string $name
 * @property string $code
 * @property float $discount
 * @property-read array $categories
 * @property-read array $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereAdminName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereDiscount($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereCategories($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereProducts($value)
 * @property string $deleted_at
 * @property \Carbon\Carbon $valid_until
 * @property boolean $free_shipping
 * @property boolean $cumulative
 * @property boolean $enabled
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereValidUntil($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereFreeShipping($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereCumulative($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Coupon whereEnabled($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Order[] $orders
 */
class Coupon extends Model {

	use SoftDeletes;

	protected $table = 'coupons';

	public $timestamps = true;

	protected $fillable = [
		'admin_name',
		'name',
		'code',
		'discount',
		'categories',
		'products',
		'roles',
		'single_use',
	];

	protected $casts = [
		'discount' => 'float',
		'max_uses' => 'integer',
		'uses' => 'integer',
	];

	protected $dates = [
		'created_at',
		'updated_at',
		'valid_until',
	];

	public function setProductsAttribute($array) {
		if ($array == null) return;

		if (is_string($array)) {
			$array = explode(',', $array);
		}

		$this->attributes['products'] = ',' .implode(',', $array) .',';
	}

	/**
	 * @return array
	 */
	public function getProductsAttribute() {
		$products = [];
		foreach(explode(',', trim($this->attributes['products'], ',')) as $id) {
			if ( ! $id) continue;
			$products[] = $id;
		}
		return $products;
	}

	public function setCategoriesAttribute($array) {
		if ($array == null) return;

		if (is_string($array)) {
			$array = explode(',', $array);
		}

		$this->attributes['categories'] = ',' .implode(',', $array) .',';
	}

	/**
	 * @return array
	 */
	public function getCategoriesAttribute() {
		$cats = [];
		foreach(explode(',', trim($this->attributes['categories'], ',')) as $id) {
			$id = trim($id);
			if ( ! $id) continue;
			$cats[] = $id;
		}
		return $cats;
	}

	public function setRolesAttribute($array) {
		if ($array == null) return;

		if (is_string($array)) {
			$array = explode(',', $array);
		}

		$this->attributes['roles'] = ',' .implode(',', $array) .',';
	}

	/**
	 * @return array
	 */
	public function getRolesAttribute() {
		$cats = [];
		foreach(explode(',', trim($this->attributes['roles'], ',')) as $id) {
			if ( ! $id) continue;
			$cats[] = $id;
		}
		return $cats;
	}

	public function users() {
		return $this->belongsToMany('Friluft\User');
	}

	public function orders() {
		return $this->belongsToMany('Friluft\Order');
	}

}