<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Nicolaslopezj\Searchable\SearchableTrait;
use Friluft\Variant;

/**
 * Friluft\Product
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $name
 * @property string $slug
 * @property float $price
 * @property integer $articlenumber
 * @property string $barcode
 * @property float $inprice
 * @property integer $weight
 * @property string $description
 * @property string $summary
 * @property integer $stock
 * @property boolean $enabled
 * @property integer $sales
 * @property integer $vatgroup_id
 * @property integer $manufacturer_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Category[] $categories
 * @property-read \Friluft\Vatgroup $vatgroup
 * @property-read \Friluft\Manufacturer $manufacturer
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $relations
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Variant[] $variants
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Producttab[] $producttabs
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Image[] $images
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereArticlenumber($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereBarcode($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereInprice($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereSummary($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereStock($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereSales($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereVatgroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereManufacturerId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product enabled()
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product search($search, $threshold = null, $entireText = false)
 * @property string $variants_stock
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Tag[] $tags
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereCategories($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereVariantsStock($value)
 */
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
		'weight' => 'float',
		'stock' => 'integer',
		'price' => 'integer',
		'inprice' => 'integer',
		'variants_stock' => 'array',
	];

	protected $searchable = [
		'columns' => [
			'name' => 15,
			'summary' => 2,
		],
	];

	/**
	 * This is the full path to this product which includes the entire depth of the category tree.
	 * It's stored here as a simple "cache".
	 *
	 */
	public $path = null;

	/**
	 * @var Collection
	 */
	private $categoriesCollection;

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

	public function isEnabled() {
		return $this->enabled == '1';
	}

	public function isInStock($options = []) {
		return $this->getStock($options) > 0;
	}

	public function getStock($options = []) {
		if (count($this->variants) == 0) return $this->stock;

		$stockID = [];
		foreach($options as $id => $value) {
			# get variant
			$variant = $this->variants()->where('name', '=', $id)->first();
			if ( ! $variant) {
				$variant = Variant::find($id);
			}
			if ($variant) {
				# get id of this value
				foreach($variant->data['values'] as $val) {
					if ($value == $val['name'] || $value == $val['id']) {
						$id = $val['id'];
					}
				}
				$stockID[] = $id;
			}
		}
		$stockID = implode('_', $stockID);
		if ( ! isset($this->variants_stock[$stockID])) return -1;
		return $this->variants_stock[$stockID];
	}

	public function getEnabledAttribute() {
		return '' + $this->attributes['enabled'];
	}

	public function getCategoriesAttribute() {
		if ( ! isset($this->categoriesCollection)) {
			$array = explode(',', trim($this->attributes['categories'], ','));
			$this->categoriesCollection = Category::whereIn('id', $array)->get();
		}

		return $this->categoriesCollection;
	}

	public function setCategoriesAttribute($categories) {
		$this->categoriesCollection = null;

		if (is_string($categories)) {
			$array = explode(',', $categories);
			sort($array, SORT_ASC);
		}
		else if ($categories instanceof Collection) {
			$collection = $categories;
			$array = [];
			foreach($collection as $cat) {
				$array[] = $cat->id;
			}
		}else if (is_array($categories)) {
			$array = $categories;
			sort($array, SORT_ASC);
		}

		$this->attributes['categories'] = ',' .implode(',', $array) .',';
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
		return $this->belongsToMany('Friluft\Variant')->orderBy('id', 'asc')->withPivot('data');
	}

	public function getVariantsTree() {
		$tree = [];

		$current = &$tree;
		foreach($this->variants as $key => $variant) {
			$node = ['variant' => $variant, 'children' => []];
			$current[] = $node;
			$current = &$node['children'];
		}

		return $tree;
	}

	public function producttabs() {
		return $this->hasMany('Friluft\Producttab');
	}

	public function images() {
		return $this->hasMany('Friluft\Image');
	}

	public function tags() {
		return $this->belongsToMany('Friluft\Tag');
	}

	public function sell($amount = 1, $variants = null) {
		$this->sales += $amount;

		# variant?
		if (is_array($variants)) {
			$stockID = [];
			foreach($variants as $id => $value) {
				$variant = Variant::find($id);
				if ($variant) {
					
				}
			}

		}else {
			$this->stock -= $amount;
		}

		$this->save();
	}

	public function getImageURL() {
		$img = $this->images()->first();
		if ($img != null) {
			return asset('images/products/' .$img->name);
		}

		return null;
	}

	public function getPath() {
		if (!isset($this->path)) {

			if (is_object($this->categories)) {
				$parent = $this->categories->first();
				if ($parent) {
					$this->path = $parent->getPath() . '/' . $this->slug;
					return $this->path;
				}
			}
			$this->path = $this->slug;
		}

		return $this->path;
	}


	public function getAvailablity() {
		if ( ! $this->variants->isEmpty()) {
			# the map will contain the availability
			$map = [];

			# get stock (variants stock)
			$stock = $this->variants_stock;
			if ( ! $stock) {
				$stock = [];
			}

			# get the root variant
			$rootVariant = $this->variants[0];

			if (count($this->variants) > 1) {
				foreach($rootVariant->data['values'] as $rootValue) {
					foreach($this->variants as $variant) {
						if ($rootVariant == $variant) continue;

						foreach($variant['data']['values'] as $value) {
							$stockID = $rootValue['id'] .'_' .$value['id'];
							$s = 0;
							if (isset($stock[$stockID])) {
								$s = $stock[$stockID];
							}
							$map[$stockID] = $s;
						}
					}
				}
			}else {
				foreach($rootVariant->data['values'] as $value) {

					$stockID = $value['id'];

					$s = 0;
					if (isset($stock[$stockID])) {
						$s = $stock[$stockID];
					}
					$map[$stockID] = $s;
				}
			}

			return $map;
		}

		return $this->stock;
	}

	/**
	 * Get availability (current stuck minus the quantity of the same item in the orders that are NOT ready_for_shipping)
	 * @param null $options
	 * @return int
	 */
	public function getAvailability($options = null) {
		if (count($this->variants) == 0) {
			$stock = $this->stock;

			foreach(Order::whereNotNull('reservation')->where('status', '!=', ['ready_for_sending'])->get() as $order) {
				foreach($order->items as $item) {
					if ($item['type'] == 'physical') {
						if ($item['reference']['id'] == $this->id) {
							# does this item have the same options?
							if (empty($variants) && empty($item['reference']['options']['variants'])) {
								$stock -= $item['quantity'];
							}else {
								$same = true;
								foreach($variants as $key => $value) {
									if (!isset($item['options']['variants'][$key])) {
										$same = false;
										continue;
									}

									if ($item['options']['variants'][$key] != $value) {
										$same = false;
										continue;
									}
								}

								if ($same) $stock -= $item['quantity'];
							}
						}
					}
				}
			}
		}

		if ($stock < 0) $stock = 0;
		return $stock;
	}

	public function __toString() {
		return $this->manufacturer->name .' ' .$this->name;
	}

}
