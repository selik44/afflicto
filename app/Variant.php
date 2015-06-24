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
 */
class Variant extends Model {

	public $timestamps = false;

	protected $table = 'variants';

	protected $casts = [
		'data' => 'array',
	];

	protected $fillable = ['name', 'data'];

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

}
