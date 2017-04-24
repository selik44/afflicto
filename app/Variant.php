<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Variant
 *
 * @property integer $id
 * @property string $name
 * @property string $data
 * @property integer $product_id
 * @property-read \Friluft\Product $product
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Variant whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Variant whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Variant whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Variant whereProductId($value)
 * @property string $admin_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Variant whereAdminName($value)
 * @property boolean $filterable
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Variant whereFilterable($value)
 * @mixin \Eloquent
 */
class Variant extends Model {

	public $timestamps = false;

	protected $table = 'variants';

	protected $casts = [
		'data' => 'array',
		'filterable' => 'boolean',
	];

	protected $fillable = ['name', 'data'];

	public function products() {
		return $this->belongsToMany('Friluft\Product')->withPivot('data');
	}

	public function getValueName($id) {
		foreach($this->data['values'] as $value) {
			if ($value['id'] == $id) return $value['name'];
		}

		return 'Error.';
	}

	public function save(array $options = [])
	{
		$ret = parent::save($options);

		# make sure all the products that have this variant have a valid stock for all the choices.
		foreach($this->products as $product) {
			# get stock
			$stock = $product->variants_stock;

			# loop through all the variant choices
			foreach($product->getVariantChoices() as $choice) {
				if ( ! isset($stock[$choice['id']])) {
					$stock[$choice['id']] = 0;
				}
			}

			# set the stock & save
			$product->variants_stock = $stock;
			$product->save();
		}

		return $ret;
	}


}
