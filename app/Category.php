<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * Friluft\Category
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $slug
 * @property integer $order
 * @property integer $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Category[] $children
 * @property-read \Friluft\Category $parent
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Category root()
 */
class Category extends Model {

	use SearchableTrait;

	protected $table = 'categories';

	protected $fillable = ['name', 'slug', 'parent_id'];

	protected $searchable = [
		'columns' => [
			'name' => 15,
			'slug' => 10
		]
	];

	public function getLevel() {
		$level = 0;
		$p = $this->parent;
		if ($p) {
			$level++;
			$level += $p->getLevel();
		}

		return $level;
	}

	public function scopeRoot($query) {
		return $query->where('parent_id', '=', null);
	}

	public function getRoot() {
		$root = $this;

		$p = $root->parent;
		if ($p) {
			$root = $p->getRoot();
		}

		return $root;
	}

	public function products() {
		return $this->belongsToMany('Friluft\Product');
	}

	/**
	 * Get all the products in this category, plus all categories in child categories.
	 * @param  boolean $disabled Whether to include disabled products.
	 * @return Array array of Project models.
	 */
	public function nestedProducts($includeDisabled = false) {
		$array = [];

		$path = $this->getPath();

		$collection = ($includeDisabled) ? $this->products : $this->products()->enabled()->get();
		foreach($collection as $p) {
			# help the model with calculating it's path
			# otherwise, each model needs to traverse up to their root category.
			$p->path = $path .'/' .$p->slug;
			$array[$p->id] = $p;
		}

		foreach($this->children as $child) {
			foreach($child->nestedProducts() as $p) {
				$array[$p->id] = $p;
			}
		}

		return $array;
	}

	public function children() {
		return $this->hasMany('Friluft\Category', 'parent_id');
	}

	public function parent() {
		return $this->belongsTo('Friluft\Category', 'parent_id');
	}

	public function getPath() {
		$slugs = [];

		$slugs = [$this->slug];

		$last = false;
		$p = $this->parent;
		while(!$last) {
			if ($p) {
				$slugs[] = $p->slug;
				$p = $p->parent;
			}else {
				$last = true;
			}
		}

		return 'store/' .implode('/', array_reverse($slugs));
	}

	public function renderMenuItem($path, $classes = []) {
		if (is_string($classes)) $classes = explode(' ', $classes);

		$title = ucfirst(strtolower($this->name));

		$path = trim($path, '/') .'/' .$this->slug;

		if (\Request::is($path .'*')) {
			$classes[] = 'current';
		}

		$classes = array_unique($classes);
		return '<a class="' .implode(' ', $classes) .'" href="' .url($path) .'">' .$title .'</a>';
	}

	public function renderMenu($path = '/store', $levels = 0) {
		return $this->renderChildren($path, $levels);
	}

	public function isCurrentRoute() {
		return \Request::is($this->getPath());
	}

	private function renderChildren($path = '/store', $levels = 0, $children = null) {
		$path = rtrim($path, '/') .'/' .$this->slug;

		$classes = '';
		if (\Request::is($path)) {
			$classes = 'visible';
		}

		$str = '';
		foreach($this->children()->orderBy('order', 'asc')->get() as $item) {

			$str .= '<li>';
			$str .= $item->renderMenuItem($path);
			
			if ($levels > 0) {
				if ($item->children()->count() > 0) {
					$str .= '<ul class="submenu ' .$classes .'">' .$item->renderChildren($path, $levels - 1) .'</ul>';
				}
			}
			$str .= '</li>';
		}

		return $str;
	}

	public function renderSortableList() {
		$str = '<li data-id="' .$this->id .'">';
		$str .= '<div class="item">';
			$str .= '<div class="line"></div>';
			$str .= '<span class="handle"><i class="fa fa-bars"></i></span>';
			$str .= '<div class="info"><a class="name" href="#category-' .$this->id .'">' .htmlentities($this->name) .'</a> <code class="slug" title="slug">' .htmlentities($this->slug) .'</code></div>';
			$str .= '<span class="arrow"><i class="fa fa-chevron-down"></i></span>';
		$str .= '</div>';

		$str .= '<ul class="flat sortable"><div class="dummy-item"></div>';
		foreach($this->children()->orderBy('order', 'asc')->get() as $child) {
			$str .= $child->renderSortableList();
		}
		$str .= '</ul>';

		return $str .'</li>';
	}

}
