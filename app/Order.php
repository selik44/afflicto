<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Friluft\Order
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_id
 * @property string $klarna_id
 * @property string $status
 * @property string $reservation
 * @property string $items
 * @property float $total_price_excluding_tax
 * @property float $total_price_including_tax
 * @property float $total_tax_amount
 * @property string $purchase_country
 * @property string $purchase_currency
 * @property string $locale
 * @property \Carbon\Carbon $completed_at
 * @property string $billing_address
 * @property string $shipping_address
 * @property string $klarna_status
 * @property-read \Friluft\User $user
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereKlarnaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereReservation($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereItems($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereTotalPriceExcludingTax($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereTotalPriceIncludingTax($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereTotalTaxAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order wherePurchaseCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order wherePurchaseCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereCompletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereBillingAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereShippingAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereKlarnaStatus($value)
 * @property boolean $activated
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\OrderEvent[] $orderEvents
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereActivated($value)
 * @property string $shipment_number
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereShipmentNumber($value)
 * @property integer $coupon_id
 * @property-read \Friluft\Coupon $coupon
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Order whereCouponId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Coupon[] $coupons
 */
class Order extends Model {

	use SoftDeletes;

	protected $table = 'orders';

	protected $dates = ['created_at', 'updated_at', 'completed_at'];
	
	protected $casts = [
		'items' => 'array',
		'total_price_excluding_tax' => 'float',
		'total_price_including_tax' => 'float',
		'total_tax_amount' => 'float',
		'billing_address' => 'array',
		'shipping_address' => 'array',
		'activated' => 'boolean',
	];

	public function user() {
		return $this->belongsTo('Friluft\User');
	}

	public function getTotal() {
		$total = 0;

		if ( ! $this->items) return 0;

		foreach($this->items as $item) {
			$total += $item['total_price_including_tax'];
		}

		return round($total);
	}

	public function getWeight() {
		$weight = 0;

		if ( ! $this->items) return 0;

		foreach($this->items as $item) {
			if ($item['type'] == 'shipping_fee') continue;
			$model = Product::find($item['reference']['id']);
			$weight += $model->weight * $item['quantity'];
		}
		return $weight;
	}

	public function getProfit() {
		$profit = 0;
		foreach($this->items as $item) {
			if ( ! isset($item['reference']) || $item['type'] !== 'physical') continue;
			$product = Product::withTrashed()->find($item['reference']['id']);
			if ( ! $product) {
				continue;
			}
			$profit += $item['total_price_excluding_tax'] - $product->inprice * $item['quantity'];
		}

		return $profit;
	}

	public function getShipping() {
		foreach($this->items as $item) {
			if ($item['type'] == 'shipping_fee') {
				return $item;
			}
		}
		return null;
	}

    public function orderEvents() {
        return $this->hasMany('Friluft\OrderEvent');
    }

	public function coupons() {
		return $this->belongsToMany('Friluft\Coupon');
	}

    public function getHumanName() {
        $firstItem = null;
        foreach($this->items as $item) {
            if ($item['type'] == 'shipping_fee') {
                continue;
            }

            $firstItem = $item['name'];
			break;
        }

        if (count($this->items) > 1) {
            $c = count($this->items) - 2;
			if ($c > 0) {
				return $firstItem .' and ' .$c .' others.';
			}else {
				return $firstItem;
			}
        }else {
            return $firstItem;
        }
    }

	public function save(array $options = []) {
		$this->profit = $this->getProfit();
		parent::save($options);
	}

}
