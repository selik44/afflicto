<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Product extends Model {

	use SearchableTrait, SoftDeletes;

	protected $table = 'products';

	protected $dates = ['created_at', 'updated_at'];

	protected $casts = [
		'enabled' => 'boolean',
		'weight' => 'float',
		'price' => 'float',
		'in_price' => 'float',
		'tax_percentage' => 'float',
		'stock' => 'integer',
		'images' => 'array',
	];

	protected $searchable = [
		'columns' => [
			'description' => 2,
			'brand' => 10,
			'name' => 15,
			'model' => 20,
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

	public function categories() {
		return $this->belongsToMany('Friluft\Category');
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
