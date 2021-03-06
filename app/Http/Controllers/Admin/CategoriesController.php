<?php namespace Friluft\Http\Controllers\Admin;

use Former;
use Friluft\Image;
use Illuminate\Http\Request;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Category;
use Input;
use DB;
use Laratable;
use Redirect;

class CategoriesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$table = Laratable::make(Category::query(), [
			'#' => 'id',
			'Name' => 'name',
			'Slug' => 'slug',
			'Discount' => ['discount', function($model) {
				return $model->discount .'%';
			}],
			'Parent' => ['parent_id', function($cat) {
				if ($cat->parent) {
					return '<a href="' .route('admin.categories.edit', $cat->parent) .'">' .e($cat->parent->name) .'</a>';
				}else {
					return 'None';
				}
			}],
			'Children' => ['children', function($cat) {
				$c = $cat->children()->count();
				return ($c > 0) ? $c : 'None';
			}],
			'Updated' => 'updated_at diffForHumans',
		]);

		$table->sortable(true, ['name','slug','parent_id', 'updated_at']);

		$table->editable(true, url('admin/categories/{id}/edit'));

		$table->destroyable(true, url('admin/categories/{id}'));

		$table->filterable(true);

		$table->addFilter('name', 'search');

		$table->paginate(true);

		return view('admin.categories_index')
			->with([
				'table' => $table->render(),
				'filters' => $table->buildFilters()->addClass('inline'),
				'pagination' => $table->paginator->render(),
			]);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->view('admin.categories_create')->with('categories', Category::orderBy('order', 'asc')->get());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$category = new Category(Input::only('name', 'slug'));
		$category->meta_description = Input::get('meta_description', null);
		$category->meta_keywords = Input::get('meta_keywords', null);

		if (Input::has('parent_id')) {
			$parent_id = Input::get('parent_id');
			if (is_numeric($parent_id)) {
				$category->parent_id = $parent_id;
			}
		}

		$category->discount = Input::get('discount', 0);

		$category->save();

		if (Input::has('continue')) {
			return redirect('admin/categories/create');
		}

		return redirect('admin/categories');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Category $category)
	{
		Former::populate($category);

		return view('admin.categories_edit')
			->with([
				'category' => $category,
				'categories' => Category::all(),
			]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Category $category)
	{
		$category->name = Input::get('name');
		$category->slug = Input::get('slug');
		$category->discount = Input::get('discount', 0);
		$category->meta_description = Input::get('meta_description', null);
		$category->meta_keywords = Input::get('meta_keywords', null);

		$parent_id = Input::get('parent_id', 'null');
		if (is_numeric($parent_id)) $category->parent_id = $parent_id;

		# banner?
		if (Input::hasFile('banner')) {
			$file = Input::file('banner');

			$filename = $file->getClientOriginalName();
			$file->move(public_path('images'), $filename);

			$banner = $category->banner;
			if ( ! $banner) {
				$banner = new Image();
				$banner->type = 'category_banner';

			}

			$banner->name = $filename;
			$banner->save();

			$category->banner()->associate($banner);
		}

		$category->save();

		return Redirect::back()->with('success', 'Category updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Category $cat)
	{
		$cat->delete();

		return Redirect::to('admin/categories')->with('success', 'Category deleted.');
	}

	public function tree() {
		return $this->view('admin.categories_tree')->with('categories', Category::root()->get());
	}

	public function tree_getProducts(Category $category) {
		return view('admin.partial.products_list')
			->with([
				'category' => $category
			]);
	}

	private function updateItem($item, $parent = null) {
		DB::table('categories')
			->where('id', '=', $item->id)
			->update([
				'parent_id' => $parent,
				'order' => $item->order,
			]);

		foreach($item->children as $child) {
			$this->updateItem($child, $item->id);
		}
	}

	public function tree_update() {
		$tree = json_decode(Input::get('tree'));
		foreach($tree as $item) {
			$this->updateItem($item, null);
		}
		return redirect('admin/categories/tree')->with('success', 'Categories updated!');
	}
}
