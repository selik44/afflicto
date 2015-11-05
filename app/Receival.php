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

	protected $table = 'receivals';

	protected $casts = [
		'products' => 'array',
		'received' => 'boolean',
	];

	protected $dates = ['created_at', 'updated_at', 'deleted_at', 'arrived_at', 'expected_arrival'];

	public function manufacturer() {
		return $this->belongsTo('Friluft\Manufacturer');
	}

	public function parent() {
		return $this->belongsTo('Friluft\Receival', 'receival_id', 'id');
	}

	public function children() {
		return $this->hasOne('Friluft\Receival', 'receival_id', 'id');
	}

	public function getProductsAttribute() {
		$products = $this->attributes['products'];
		if ( ! $products) {
			return [];
		}
		return json_decode($products, true);
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

	/**
	 * Process the receival by updating the products array as well as incrementing the stock of the products.
	 *
	 * @param $input
	 */
	public function receive($input) {
		#------ set received ------#
		$products = $this->products;
		foreach($products as &$product) {
			$model = Product::find($product['id']);

			# has variants?
			if ($model->hasVariants()) {
				# get received
				$product['received'] = [];
				foreach($model->getVariantChoices() as $choice) {
					$inputName = $model->id .'_' .$choice['id'];
					$product['received'][$choice['id']] = isset($input[$inputName]) ? $input[$inputName] : 0 ;
				}
			}else {
				$product['received'] = (isset($input[$model->id])) ? (int) $input[$model->id] : 0;
			}
		}

		# update products array with the received data
		$this->products = $products;
		$this->received = true;
		$this->arrived_at = new Carbon();
		$this->save();

		#------ update stock ------#
		foreach($this->products as $product) {
			$model = Product::find($product['id']);

			if ($model->hasVariants()) {
				# get stock
				$stock = $model->variants_stock;

				# loop through all the variant choices
				foreach($model->getVariantChoices() as $choice) {

					$stock[$choice['id']] += $product['received'][$choice['id']];
				}

				# update stock
				$model->variants_stock = $stock;
			}else {
				# update stock
				$model->stock += $product['received'];
			}

			# save the model
			$model->save();
		}
	}

	/**
	 * Generates a new "rest" receival based on the discrepancy between the amount ordered and amount received.
	 */
	public function generateRest() {
		$rest = $this->products;
		$totalMissing = 0;

		foreach($rest as $key => $item) {
			$model = Product::find($item['id']);

			# get missing
			$missing = 0;

			if ($model->hasVariants()) {

				foreach($item['order'] as $id => $amount) {
					$ordered = $amount;
					$received = $item['received'][$id];

					# get difference
					$diff = $ordered - $received;
					if ($diff < 0) $diff = 0;

					# add to total missing for this product
					$missing += $diff;

					$rest[$key]['order'][$id] = $diff;
				}

			}else {
				$ordered = $item['order'];
				$received = $item['received'];

				# get difference
				$diff = $ordered - $received;
				if ($diff < 0) $diff = 0;

				# add to total missing for this product
				$missing += $diff;

				$rest[$key]['order'] = $diff;
			}

			$totalMissing += $missing;

			# none missing, if so, remove it entirely.
			if ($missing == 0) {
				# remove it entirely
				unset($rest[$key]);
			}else {
				if (isset($item['received'])) unset($rest[$key]['received']);
			}
		}

		# generate rest receival?
		if ($totalMissing > 0) {
			$receival = new Receival();
			$receival->manufacturer()->associate($this->manufacturer);
			$receival->parent()->associate($this);
			$receival->rest = true;
			$receival->expected_arrival = new Carbon();
			$receival->products = $rest;
			$receival->save();
			return $receival;
		}

		return null;
	}

}
