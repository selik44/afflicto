<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Category;
use Friluft\Manufacturer;
use Friluft\Product;
use Friluft\Tag;
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
            'Stock' => 'stock',
            'Price' => 'price',
            'Category' => ['category', function($model, $column, $value) {
                if ($model->categories()->first() != null) {
                    return $model->categories()->first()->name;
                }

                return 'None';
            }],
            'Enabled' => ['enabled', function($model, $column, $value) {
                return ($model->enabled) ? '<span class="color-success">Yes</span>' : '<span class="color-error">no</span>';
            }],
			'Updated' => 'updated_at diffForHumans',
		]);

		$table->editable(true, url('admin/products/{id}/edit'));
		$table->destroyable(true, url('admin/products/{id}'));
		$table->sortable(true, [
			'name','price','stock','enabled','updated_at', 'category'
		]);
        $table->selectable(true);

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
        $tags = Tag::all();

		return $this->view('admin.products_create')
		->with([
			'categories' => $cats,
			'manufacturers' => $mfs,
			'vatgroups' => $vatgroups,
			'form' => form('admin.product', ['categories' => $cats, 'manufacturers' => $mfs, 'vatgroups' => $vatgroups, 'tags' => $tags]),
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
		$p->manufacturer()->associate($manufacturer);

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
		$tags = Tag::all();

		# mock tabs
		$product->tabs = [
			['title' => 'Size Guide', 'body' => '<p>this is the <strong>size</strong> guide</p>'],
			['title' => 'Another one', 'body' => '<p>this is the <strong>other</strong> one</p>'],
		];

		return $this->view('admin.products_edit')
			->with([
				'product' => $product,
				'categories' => $cats,
				'tags' => $tags,
				'manufacturers' => $mfs,
				'vatgroups' => $vatgroups,
				'form' => form('admin.product', ['product' => $product, 'categories' => $cats, 'manufacturers' => $mfs, 'vatgroups' => $vatgroups, 'tags' => $tags]),
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

		# sync tags
		$p->tags()->sync(Input::get('tags', []));

		# success
		return Redirect::back()->with('success', 'Product updated!');
	}

	public function relate(Product $p, $related) {
		if (DB::table('product_relation')->where('product_id', '=', $p->id)->where('relation_id', '=', $related)->count() == 0) {
			$p->relations()->attach($related);
			return response('OK');
		}

		return response("ERROR: Relation already exists.");
	}

	public function unrelate(Product $p, $related) {
		$p->relations()->detach($related);
		return response('OK');
	}

	public function destroy(Product $product)
	{
		$product->delete();
		return redirect(route('admin.products.index'))->with('success', 'Product deleted.');
	}

}
