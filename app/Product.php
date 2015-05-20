<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Product extends Model {

	use SearchableTrait, SoftDeletes;

	protected $table = 'products';

	protected $dates = ['created_at', 'updated_at'];

	protected $fillable = [
		'name',
		'slug',
		'price',
		'articlenumber',
		'barcode',
		'inprice',
		'weight',
		'description',
		'summary',
		'stock',
		'enabled',
		'vatgroup_id',
		'manufacturer_id',
	];

	protected $casts = [
		'enabled' => 'boolean',
		'weight' => 'float',
		'price' => 'float',
		'in_price' => 'float',
		'stock' => 'integer',
		'images' => 'array',
	];

	protected $searchable = [
		'columns' => [
			'name' => 15,
			'summary' => 2,
		],
	];

	/**
	 * This is the full path to this product which includes the entire depth of the category tree.
	 * It's stored here as a simple "cache" mechanism, for performance reasons.
	 *
	 */
	public $path = null;

	public function scopeEnabled($query) {
		return $query->where('enabled', '=', '1');
	}

	public function getFormattedWeight() {
		$w = $this->weight;
		if ($w > 1000) {
			return ($w / 1000) ."<sub>kg</sub>";
		}else {
			return $w .'<sub>g</sub>';
		}

		return $this->weight;
	}

	public function setEnabledAttribute($value) {
		if (is_string($value)) {
			if (is_numeric($value)) {
				$this->attributes['enabled'] = $value;
			}else if ($value == 'true') {
				$this->attributes['enabled'] = 1;
			}else if ($value == 'false') {
				$this->attributes['enabled'] = false;
			}
		}else if (is_bool($value)) {
			$this->attributes['enabled'] = ($value) ? 1 : 0;
		}
	}

	public function categories() {
		return $this->belongsToMany('Friluft\Category');
	}

	public function vatgroup() {
		return $this->belongsTo('Friluft\Vatgroup');
	}

	public function manufacturer() {
		return $this->belongsTo('Friluft\Manufacturer');
	}

	public function relations() {
		return $this->belongsToMany('Friluft\Product', 'product_relation', 'product_id', 'relation_id');
	}

	public function variants() {
		return $this->hasMany('Friluft\Variant');
	}

	public function producttabs() {
		return $this->hasMany('Friluft\Producttab');
	}

	public function sell($amount = 1) {
		$this->sales += $amount;
		$this->save();
	}

	public function getPath() {
		if (!isset($this->path)) {
			$parent = $this->categories()->first();
			if ($parent) {
				$this->path = $parent->getPath() .'/' .$this->slug;
			}else {
				$this->path = 'store/' .$this->slug;
			}
		}

		return $this->path;
	}

	/**
	 * Get the path to an image, given the index of the image (relative to the images array).
	 * @param int $index
	 * @return null|string
	 */
	public function getImagePath($index = 0) {
		if (is_numeric($index)) {
			$images = $this->images;
			if (isset($images[$index]) > 0) {
				return asset('/images/products/' .$images[0]);
			}
		}else {
			return asset('images/products/' .$index);
		}
		return null;
	}

}
