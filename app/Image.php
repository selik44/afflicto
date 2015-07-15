<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Image
 *
 * @property integer $id
 * @property string $name
 * @property integer $order
 * @property array $data
 * @property integer $product_id
 * @property string $type
 * @property-read \Friluft\Product $product
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereType($value)
 */
class Image extends Model {

	public $timestamps = false;
	protected $table = 'images';

	protected $casts = [
		'data' => 'json',
	];

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

}
