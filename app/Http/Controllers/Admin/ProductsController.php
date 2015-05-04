<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Category;
use Friluft\Product;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Datatable;
use Carbon\Carbon;

class ProductsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($page = 1, $column = 'id', $direction = 'asc')
	{

		$table = Datatable::make('products', 'Friluft\Product', [
			'id' => '#',
			'name' => 'Name',
			'brand' => 'Brand',
			'model' => 'Model',
			'price' => 'Price',
			'stock' => 'Stock',
			'enabled' => 'Enabled',
			'updated_at' => 'Updated',
		]);

		$table->option('url', url() .'/admin/products/{page}/{column}/{direction}');

		$table->paginate(15, $page);
		$table->sort(['id', 'name', 'brand', 'model', 'price', 'stock', 'enabled', 'updated_at'], $column, $direction);

		$table->rewrite('enabled', function($row) {
			if ($row['enabled'] == '1') return '<span class="color-success">Yes</span>';
			return '<span class="color-error">No</span>';
		});

		$table->rewrite('updated_at', function($row) {
			$c = new Carbon($row['updated_at']);
			return $c->diffForHumans();
		});

		return $this->view('admin.products_index')->with('table', $table->display());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->view('admin.products_create')->with('categories', Category::all());
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
			$r = Redirect::to('admin/products/create')->withInput(Input::all());
		}else {
			$r = Redirect::to('admin/products/' .$p->id);
		}

		return $r->with('success', 'Product "' .htmlentities($p->name) .'" created.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
