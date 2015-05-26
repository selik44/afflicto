<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Category;
use Friluft\Manufacturer;
use Friluft\Product;
use Friluft\Taxgroup;
use Friluft\Vatgroup;
use Illuminate\Http\Request;
use Input;
use Laratable;
use Redirect;
use Carbon\Carbon;
use DB;
use Former;

class ProductsController extends Controller {

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

	public function create()
	{
		$cats = Category::all();
		$mfs = Manufacturer::all();
		$vatgroups = Vatgroup::all();

		return $this->view('admin.products_create')
		->with([
			'categories' => $cats,
			'manufacturers' => $mfs,
			'vatgroups' => $vatgroups,
			'form' => form('admin.product', ['categories' => $cats, 'manufacturers' => $mfs, 'vatgroups' => $vatgroups]),
		]);
	}

	public function store()
	{
		$p = new Product(Input::only('name', 'slug', 'inprice', 'price', 'weight', 'summary', 'articlenumber', 'barcode', 'enabled', 'stock'));

		if (Input::get('enabled', 'off') == 'on') {
			$p->enabled = true;
		}else {
			$p->enabled = false;
		}

		# set vatgroup
		$vatgroup = Vatgroup::find(Input::get('vatgroup'));
		$p->vatgroup()->associate($vatgroup);

		# set manufacturer
		$manufacturer = Manufacturer::find(Input::get('manufacturer'));
		$p->manufacturer()->associate($vatgroup);

		# save it
		$p->save();

		# sync categories
		$p->categories()->sync(Input::get('categories', []));

		# continue?
		if (Input::has('continue')) {
			$r = Redirect::route('admin.products.create');
		}else {
			$r = Redirect::route('admin.products.edit', $p);
		}

		return $r->with('success', 'Product "' .e($p->name) .'" created.');
	}


	public function edit(Product $product)
	{
		Former::populate($product);

		$cats = Category::all();
		$mfs = Manufacturer::all();
		$vatgroups = Vatgroup::all();

		# mock tabs
		$product->tabs = [
			['title' => 'Size Guide', 'body' => '<p>this is the <strong>size</strong> guide</p>'],
			['title' => 'Another one', 'body' => '<p>this is the <strong>other</strong> one</p>'],
		];

		return $this->view('admin.products_edit')
			->with([
				'product' => $product,
				'categories' => $cats,
				'manufacturers' => $mfs,
				'vatgroups' => $vatgroups,
				'form' => form('admin.product', ['product' => $product, 'categories' => $cats, 'manufacturers' => $mfs, 'vatgroups' => $vatgroups]),
			]);
	}

	public function update(Product $p)
	{
		$p->name = Input::get('name');
		$p->slug = Input::get('slug');
		$p->articlenumber = Input::get('articlenumber');
		$p->barcode = Input::get('barcode');
		$p->summary = Input::get('summary');
		$p->description = Input::get('description');
		$p->stock = Input::get('stock', 0);
		$p->enabled = (Input::has('enabled')) ? true : false;
		$p->weight = Input::get('weight', 0);
		$p->inprice = Input::get('inprice', 0);
		$p->price = Input::get('price', 0);
		$p->manufacturer_id = Input::get('manufacturer');
		$p->vatgroup_id = Input::get('vatgroup');

		# save
		$p->save();

		# sync categories
		$p->categories()->sync(Input::get('categories', []));

		# success
		return Redirect::back()->with('success', 'Product updated!');
	}

	public function destroy(Product $product)
	{
		$product->delete();
		return redirect(route('admin.products.index'))->with('success', 'Product deleted.');
	}

}
