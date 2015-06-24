<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Producttab
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $title
 * @property string $body
 * @property integer $order
 * @property-read \Friluft\Product $product
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Producttab whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Producttab whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Producttab whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Producttab whereBody($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Producttab whereOrder($value)
 */
class Producttab extends Model {

	public $timestamps = false;

	protected $table = 'producttabs';

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

}
