<?php namespace Friluft\Laratables;

use Friluft\Category;
use Gentlefox\Laratables\Filters\SelectFilter;
use Illuminate\Http\Request;

class CategoryFilter extends SelectFilter {

	public function __construct(Request $request, $column)
	{
		parent::__construct($request, $column);

		$this->values['*'] = 'Any';

		foreach(Category::root()->orderBy('order', 'asc')->get() as $cat) {
			$this->addcategory($cat);
		}
	}

	private function addCategory($category) {
		$this->values[$category->id] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $category->getLevel()) .$category->name;
		foreach($category->children as $child) {
			$this->addCategory($child);
		}
	}

	public function apply(\Illuminate\Database\Eloquent\Builder $query)
	{
		if ( $this->value == '*') return;

		$category = Category::find($this->value);

		$categories = [];
		foreach($category->nestedChildren() as $cat) {
			$categories[] = $cat->id;
		}

		$query->whereIn('category_id', $categories);
	}

}