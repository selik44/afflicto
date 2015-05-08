<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Category;
use Friluft\Product;
use Illuminate\Http\Request;
use Input;
use Laratable;
use Redirect;
use Carbon\Carbon;
use DB;
use Former;

class ProductsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$table = Laratable::make(Product::query(), [
			'#' => 'id',
			'Name' => 'name',
			'Price' => 'price',
			'Stock' => 'stock',
			'Enabled' => ['enabled', function($model, $column, $value) {
				return ($model->enabled) ? '<span class="color-success">Yes</span>' : '<span class="color-error">no</span>';
			}],
			'Updated' => 'updated_at diffForHumans',
		]);

		$table->editable(true, url('admin/products/{id}/edit'));
		$table->destroyable(true, url('admin/products/{id}'));

		$table->sortable(true, [
			'name','price','stock','enabled','updated_at'
		]);

		return $this->view('admin.products_index')
		->with([
			'table' => $table->render(),
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
		return $this->view('admin.products_create')
		->with([
			'categories' => Category::all(),
			'form' => form('admin.product'),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$p = new Product();
		$p->name = Input::get('name');
		$p->slug = Input::get('slug');
		$p->brand = Input::get('brand');
		$p->model = Input::get('model');
		$p->description = Input::get('description');
		$p->stock = Input::get('stock', 0);
		$p->enabled = (Input::get('enabled', 'off') == 'on') ? true : false;
		$p->weight = Input::get('weight', 0);
		$p->in_price = Input::get('in_price', 0);
		$p->price = Input::get('price', 0);
		$p->tax_percentage = Input::get('tax_percentage', 0);

		$p->save();

		$p->categories()->sync(Input::get('categories', []));

		if (Input::has('continue')) {
			$r = Redirect::route('admin.products.create');
		}else {
			$r = Redirect::route('admin.products.edit', $p);
		}

		return $r->with('success', 'Product "' .e($p->name) .'" created.');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Product $product)
	{
		Former::populate($product);

		return view('admin.products_edit')
		->with([
			'product' => $product,
			'categories' => Category::all(),
			'form' => form('admin.product'),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Product $p)
	{
		$p->name = Input::get('name');
		$p->slug = Input::get('slug');
		$p->brand = Input::get('brand');
		$p->model = Input::get('model');
		$p->description = Input::get('description');
		$p->stock = Input::get('stock', 0);
		$p->enabled = (Input::get('enabled', 'off') == 'on') ? true : false;
		$p->weight = Input::get('weight', 0);
		$p->in_price = Input::get('in_price', 0);
		$p->price = Input::get('price', 0);
		$p->tax_percentage = Input::get('tax_percentage', 0);

		$p->save();

		$p->categories()->sync(Input::get('categories', []));

		return redirect(route('admin.products.index'))->with('success', 'Product updated!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($product)
	{
		$product->delete();
		return redirect(route('admin.products.index'))->with('success', 'Product deleted.');
	}

}
