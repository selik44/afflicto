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
 * @property integer $banner_id
 * @property-read \Friluft\Image $banner
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer whereBannerId($value)
 * @property bool $prepurchase_enabled
 * @property int $prepurchase_days
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer searchRestricted($search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer wherePrepurchaseDays($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Manufacturer wherePrepurchaseEnabled($value)
 * @mixin \Eloquent
 */
class Manufacturer extends Model {

	use SearchableTrait;

	public $timestamps = false;

	protected $table = 'manufacturers';

	protected $fillable = ['name', 'slug', 'prepurchase_enabled', 'prepurchase_days'];

	protected $casts = [
		'prepurchase_days' => 'integer',
	];

	protected $searchable = [
		'columns' => [
			'name' => 15,
			'slug' => 15,
		],
	];

	public function products() {
		$this->hasMany('Friluft\Product');
	}

	public function image() {
		return $this->belongsTo('Friluft\Image');
	}

	public function banner() {
		return $this->belongsTo('Friluft\Image');
	}

}