<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * Friluft\Manufacturer
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereImages($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer search($search, $threshold = null, $entireText = false)
 * @property integer $image_id
 * @property-read \Friluft\Image $image
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereImageId($value)
 * @property string $description
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereDescription($value)
 * @property boolean $always_allow_orders 
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereAlwaysAllowOrders($value)
 */
class Manufacturer extends Model {

	use SearchableTrait;

	public $timestamps = false;
	protected $table = 'manufacturers';

	protected $fillable = ['name','slug'];

	protected $searchable = [
		'columns' => [
			'name' => 15,
			'slug' => 10,
		],
	];

	public function products() {
		$this->hasMany('Friluft\Product');
	}

	public function image() {
		return $this->belongsTo('Friluft\Image');
	}

}