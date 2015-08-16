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

}
