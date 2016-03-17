<?php namespace Friluft;

use Carbon\Carbon;
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
 * @property \Illuminate\Database\Eloquent\Collection|\Friluft\Category[] $categories
 * @property \Friluft\Vatgroup $vatgroup
 * @property \Friluft\Manufacturer $manufacturer
 * @property \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $relations
 * @property \Illuminate\Database\Eloquent\Collection|\Friluft\Variant[] $variants
 * @property \Illuminate\Database\Eloquent\Collection|\Friluft\Producttab[] $producttabs
 * @property \Illuminate\Database\Eloquent\Collection|\Friluft\Image[] $images
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
 * @property string $meta_description
 * @property string $meta_keywords
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereMetaDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Product whereMetaKeywords($value)
 */
class Product extends Model {

	/**
	 * There aren't any left and one should not be able to prepurchase.
	 */
	const AVAILABILITY_BAD = 0;

	/**
	 * There aren't any left, but they're coming soon and can be prepurchased.
	 */
	const AVAILABILITY_WARNING = 1;

	/**
	 * In stock, all good.
	 */
	const AVAILABILITY_GOOD = 2;

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
		'meta_description',
		'meta_keywords',
		'children',
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

	public function scopeWithoutCompounds($query) {
		return $query->where('children', '=', null);
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

	public function getTotalStock() {
		$total = 0;

		# is this a compound product?
		if ($this->isCompound()) {
			foreach($this->getChildren() as $child) {
				$total += $child->getTotalStock();
			}
		}else if ($this->hasVariants()) {
			$stock = $this->variants_stock;
			if ($stock) {
				foreach($this->variants_stock as $stock) {
					$total += $stock;
				}
			}
		}else {
			$total = $this->stock;
		}

		return $total;
	}

	/**
	 * Get the expected arrival of new units
	 *
	 * @return Carbon|null
	 */
	public function getExpectedArrival($options = []) {
		if ($this->isCompound()) {

			$expected = null;

			foreach($this->getChildren() as $child) {
				$childExpected = $child->getExpectedArrival();
				if ($childExpected == null) {
					return null;
				}else if ($expected == null) {
					$expected = $childExpected->copy();
				}else if ($childExpected->getTimestamp() > $expected->getTimestamp()) {
					$expected = $childExpected->copy();
				}
			}

			return $expected;
		}else {
			$soon = new Carbon();
			$soon->addWeek(1);

			$expected = null;

			foreach(Receival::where('received', '=', '0')->where('expected_arrival', '>', Carbon::now()->format(Carbon::ISO8601))->get() as $receival) {
				# does this receival contain this product?
				foreach($receival->products as $product) {
					if ($product['id'] == $this->id) {
						if ($expected == null || $receival->expected_arrival->getTimestamp() < $expected->getTimestamp()) {
							$expected = $receival->expected_arrival->copy();
						}
						continue;
					}
				}
			}

			return $expected;
		}
	}

	/**
	 * Get the availability status of this product (and all their variants)
	 * @return int AVAILABILITY_BAD, AVAILABILITY_WARNING or AVAILABILITY_GOOD.
	 */
	public function getAvailability() {
		# is this a compound product?
		if ($this->isCompound()) {
			$availability = static::AVAILABILITY_GOOD;

			foreach($this->getChildren() as $child) {
				$childAvailability = $child->getAvailability();
				if ($childAvailability < $availability) {
					$availability = $childAvailability;
				}
			}

			return $availability;

		}else if ( ! $this->hasVariants() && $this->getTotalStock() > 0) {
			return static::AVAILABILITY_GOOD;
		}else {

			# get the worst stock from variants_stock
			$worstStock = null;
			if (is_array($this->variants_stock)) {
				foreach($this->variants_stock as $stock) {
					if ($stock < $worstStock || $worstStock == null) $worstStock = $stock;
				}
			}

			# good? otherwise, check if a receival exists and pre-purchase is possible.
			if ($worstStock != null && $worstStock > 0) {
				return static::AVAILABILITY_GOOD;
			}else if ($this->manufacturer != null && $this->manufacturer->prepurchase_enabled) {

				# any receivals that contain this product?
				$expectedArrival = $this->getExpectedArrival();
				if ($expectedArrival == null) {
					return static::AVAILABILITY_BAD;
				}

				# is it arriving soon enough?
				$soon = new Carbon();
				$soon->addDays($this->manufacturer->prepurchase_days);
				if ($expectedArrival->getTimestamp() <= $soon->getTimestamp()) {
					return static::AVAILABILITY_WARNING; # undo changed from warning to good
				}
			}
		}

		# nope
		return static::AVAILABILITY_BAD;
	}

	public function getStock($variants = []) {
		# did we pass in an options array? if so, get the variants array from that.
		if (isset($variants['variants'])) {
			$variants = $variants['variants'];
		}

		if ($this->isCompound()) {
			$stock = null;

			foreach($this->getChildren() as $child) {
				if ($child->variants->count() > 0) {
					$stockID = [];
					foreach($child->variants as $variant) {
						if ( ! isset($variants[$variant->id])) {
							\Log::error('Cannot get stock, missing variant option for ' .$variant->id);
						}else {
							$stockID[] = $variants[$variant->id];
						}
					}

					$stockID = implode('_', $stockID);
					if ( ! isset($child->variants_stock[$stockID])) return 'Invalid stock id: ' .implode('_', $variants);
					$s = $child->variants_stock[$stockID];
				}else {
					$s = $child->stock;
				}

				if ($stock == null || $s < $stock) $stock = $s;
			}

			return (int) $stock;
		}else {
			if ($this->variants->count() == 0) return $this->stock;

			if ( ! isset($this->variants_stock[implode('_', $variants)])) {
				return 'Invalid stock id: ' .implode('_', $variants);
			}

			return (int) $this->variants_stock[implode('_', $variants)];
		}
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

	public function sizemap() {
		return $this->belongsTo('Friluft\Sizemap');
	}

	/**
	 * Get this product's variants as a hierarchical array.
	 *
	 * @return array
	 */
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
		return $this->hasMany('Friluft\Image')->orderBy('order', 'asc');
	}

	public function tags() {
		return $this->belongsToMany('Friluft\Tag');
	}

	public function sell($amount = 1, $variants = []) {
		$this->sales += (int) $amount;

		if ($this->isCompound()) {

			foreach($this->getChildren() as $child) {
				if ($child->variants->count() > 0) {
					$stockID = [];
					foreach($child->variants as $variant) {
						if ( ! isset($variants[$variant->id])) {
							\Log::error('Cannot decrement stock, missing variant option for ' .$variant->id);
						}else {
							$stockID[] = $variants[$variant->id];
						}
					}

					$stockID = implode('_', $stockID);

					$variants_stock = $child->variants_stock;

					if ( isset($variants_stock[$stockID])) {
						$variants_stock[$stockID] -= $amount;
						$child->variants_stock = $variants_stock;
						$child->save();
					}else {
						\Log::error('Cannot decrement stock, invalid stockID: ' .$stockID);
					}

				}else {
					$child->stock -= $amount;
					$child->save();
				}
			}

		}else {
			if ($this->variants->count() == 0) {
				$this->stock -= $amount;
			}else if ($this->variants->count() > 0) {
				$stock = $this->variants_stock;
				$stockID = implode('_', $variants);
				if (isset($stock[$stockID])) {
					$stock[$stockID] -= $amount;
					$this->variants_stock = $stock;
				}else {
					\Log::error('Cannot decrement stock, invalid stockID! ' .$stockID);
				}
			}
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

	public function __toString() {
		if (isset($this->manufacturer)) {
			return $this->manufacturer->name .' ' .$this->name;
		}
		return $this->name;
	}

	/**
	 * Get discount as percentage. I.E 20% returns integer 20
	 * @return int
	 */
	public function getDiscount() {
		$discount = 0;

		# find the best discount!
		foreach($this->categories as $cat) {
			if ($cat->getDiscount() > $discount) $discount = $cat->getDiscount();
		}

		foreach($this->tags as $tag) {
			if ($tag->discount > $discount) $discount = $tag->discount;
		}

		return $discount;
	}

	public function getDiscountPrice() {
		return $this->price * (1 - $this->getDiscount() / 100);
	}

	public function hasDiscount() {
		return $this->getDiscount() > 0;
	}

	public function getChildrenAttribute() {
		if ($this->attributes['children'] == null) return [];

		$children = [];
		foreach(explode(',', $this->attributes['children']) as $child) {
			if ( ! $child) continue;
			$children[] = $child;
		}
		return $children;
	}

	public function setChildrenAttribute($array = null) {
		if ($array == null) {
			$this->attributes['children'] = null;
		}else if (is_array($array)) {
			$this->attributes['children'] = ',' .implode(',', $array) .',';
		}
	}

	/**
	 * Get children as array of models.
	 *
	 * return [Friluft\Product]
	 */
	public function getChildren() {
		$models = [];
		foreach($this->children as $id) {
			$models[] = Product::find($id);
		}
		return $models;
	}

	/**
	 * Return whether this is a compound product. I.E, if it has any children.
	 *
	 * @return bool
	 */
	public function isCompound() {
		return count($this->children) > 0;
	}

	/**
	 * Check whether this product has variants OR whether it is a compound product that has children with variants.
	 */
	public function hasVariants() {
		if ($this->isCompound()) {
			foreach($this->getChildren() as $child) {
				if ($child->variants->count() >= 0) return true;
			}
		}else {
			return $this->variants->count();
		}

		return false;
	}

	/**
	 * Returns an array of all the variants on this product as well as child products.
	 *
	 * @return array
	 */
	public function getVariants() {
		$variants = [];
		foreach($this->variants as $variant) {
			$variants[$variant->id] = $variant;
		}

		foreach($this->getChildren() as $child) {
			foreach($child->variants as $variant) {
				$variants[$variant->id] = $variant;
			}
		}

		return $variants;
	}

	/**
	 * Get an array of variant choices based on combinations of the variants on the given product.
	 * @param Product $product
	 * @return array [[id => '', name => '', stock => '', 'variants' => []], [id => ''...]...]
	 */
	public function getVariantChoices() {
		if ( ! $this->hasVariants()) {
			return [];
		}

		$choices = [];

		# only 1 variant or multiple?
		if ($this->variants->count() == 1) {
			# get the first variant
			$variant = $this->variants->first();

			# loop through the values and generate the choice
			foreach($variant->data['values'] as $value) {
				$stockID = $value['id'];
				$name = $value['name'];
				$stock = (isset($this->variants_stock[$stockID])) ? $this->variants_stock[$stockID] : 0;

				$choices[$stockID] = [
					'id' => $stockID,
					'name' => $name,
					'stock' => $stock,
					'variants' => [
						$variant,
					],
				];
			}
		}else {

			$rootVariant = $this->variants->first();
			foreach($rootVariant->data['values'] as $rootValue) {
				foreach ($this->variants as $variant) {
					if ($rootVariant == $variant) continue;

					foreach ($variant['data']['values'] as $value) {
						$stockID = $rootValue['id'] . '_' . $value['id'];
						$name = $rootValue['name'] .' ' .$value['name'];
						$stock = (isset($this->variants_stock[$stockID])) ? $this->variants_stock[$stockID] : 0;

						$choices[] = [
							'id' => $stockID,
							'name' => $name,
							'stock' => $stock,
							'variants' => [
								$rootVariant,
								$variant,
							],
						];
					}
				}
			}
		}

		return $choices;
	}

}