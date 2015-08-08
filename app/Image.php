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
 * @property integer $manufacturer_id
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereManufacturerId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Image whereData($value)
 */
class Image extends Model {

	public $timestamps = false;
	protected $table = 'images';

	protected $fillable = [
		'type',
	];

	protected $casts = [
		'data' => 'array',
	];

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

	public function getURLAttriute() {
		return public_path($this->name);
	}

	/**
	 * @return string path to a thumbnail version of this image
	 */
	public function getThumbnail() {
		$pathinfo = pathinfo($this->name);
		return $pathinfo['filename'] .'_thumbnail.' .$pathinfo['extension'];
	}

}
