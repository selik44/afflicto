<?php namespace Friluft;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Friluft\Receival
 *
 * @property integer $id the id
 * @property array $products array of products to order & receive
 * @property Carbon $when
 * @property boolean $received whether it has been received or not.
 * @property boolean $rest whether this is a rest receival or not
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Receival whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Receival whereProducts($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Receival whereWhen($value)
 */
class Receival extends Model {

	use SoftDeletes;

	public $timestamps = false;

	protected $table = 'receivals';

	protected $casts = [
		'products' => 'array',
		'received' => 'boolean',
		'rest' => 'boolean',
	];

	protected $dates = ['created_at', 'deleted_at', 'expected_arrival'];

	public function manufacturer() {
		return $this->belongsTo('Friluft\Manufacturer');
	}

	public function getSum() {
		$sum = 0;
		foreach($this->products as $product) {
			$model = Product::find($product['id']);
			if ($model->hasVariants()) {
				$count = 0;
				foreach($product['order'] as $v) {
					$count += $v;
				}
			}else {
				$count = $product['order'];
			}

			$sum += round($model->inprice * $count);
		}

		return $sum;
	}

	public function getProductsWithModels() {
		$products = $this->products;
		foreach($products as &$product) {
			$product['model'] = Product::find($product['id']);
		}
		return $products;
	}

}
