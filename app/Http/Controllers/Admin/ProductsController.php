<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Category;
use Friluft\Manufacturer;
use Friluft\Product;
use Friluft\Producttab;
use Friluft\Tag;
use Friluft\Variant;
use Friluft\Vatgroup;
use Request;
use Session;
use Input;
use Laratable;
use Redirect;
use DB;
use Former;

class ProductsController extends Controller {

	public function index()
	{
		# store the query settings
		Session::put('admin_products_index_query', Request::getQueryString());

		$table = Laratable::make(Product::query(), [
			'#' => 'id',
            'Name' => 'name',
            'Stock' => ['stock', function($model) {
				if ($model->variants->count() > 0) {
					return array_sum($model->variants_stock);
				}else {
					return $model->stock;
				}
			}],
            'Price' => 'price',
			'Manufacturer' => ['manufacturer_id', function($model, $column, $value) {
				if ( ! $model->manufacturer) return '<span class="color-error">None</span>';
				return $model->manufacturer->name;
			}],
            'Categories' => ['categories', function($model, $column, $value) {
				$cats = $model->categories;

				if (empty($cats)) {
					return 'None';
				}

				$str = '';
				foreach($cats as $cat) {
					$str .= $cat->name .', ';
				}
				return trim($str, ', ');
            }],
			'Tags' => ['_tags', function($model, $column, $value) {
				$tags = [];
				foreach($model->tags as $tag) {
					$tags[] = $tag->label;
				}

				return implode(', ', $tags);
			}],
            'Enabled' => ['enabled', function($model, $column, $value) {
                return ($model->enabled) ? '<span class="color-success">Yes</span>' : '<span class="color-error">no</span>';
            }],
			'Updated' => 'updated_at diffForHumans',
		]);

		$table->editable(true, url('admin/products/{id}/edit'));
		$table->destroyable(true, url('admin/products/{id}'));
		$table->sortable(true, [
			'name','price','stock','enabled','updated_at', 'categories','manufacturer_id',
		]);
        $table->selectable(true);

		$table->filterable(true);
		$table->addFilter('name', 'search');

		$manufacturers = ['*' => 'Any'];
		foreach(Manufacturer::orderBy('name', 'asc')->get() as $mf) {
			$manufacturers[$mf->id] = $mf->name;
		}
		$table->addFilter('manufacturer_id', 'select')->setValues($manufacturers);
		$table->addFilter('categories', 'category');

		$table->paginate(true, 13);

		return $this->view('admin.products_index')
		->with([
			'table' => $table->render(),
			'pagination' => $table->paginator->render(),
			'filters' => $table->buildFilters()->addClass('inline'),
			'categories' => Category::orderBy('parent_id', 'asc')->orderBy('name', 'asc')->get(),
		]);
	}

	public function create()
	{
		$cats = Category::all();
		$mfs = Manufacturer::all();
		$vatgroups = Vatgroup::all();
        $tags = Tag::all();
		$variants = Variant::all();

		return $this->view('admin.products_create')
		->with([
			'categories' => $cats,
			'manufacturers' => $mfs,
			'vatgroups' => $vatgroups,
			'form' => form('admin.product', ['categories' => $cats, 'manufacturers' => $mfs, 'vatgroups' => $vatgroups, 'tags' => $tags, 'variants' => $variants]),
		]);
	}

	public function store()
	{
		$p = new Product(Input::only('name', 'slug', 'inprice', 'price', 'weight', 'summary', 'articlenumber', 'barcode', 'enabled', 'stock'));

		$p->categories = Input::get('categories', []);

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
		$variants = Variant::all();

		return $this->view('admin.products_edit')
			->with([
				'product' => $product,
				'categories' => $cats,
				'tags' => $tags,
				'manufacturers' => $mfs,
				'vatgroups' => $vatgroups,
				'form' => form('admin.product', [
					'product' => $product,
					'categories' => $cats,
					'manufacturers' => $mfs,
					'vatgroups' => $vatgroups,
					'tags' => $tags,
					'variants' => $variants
				]),
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
		$p->price = Input::get('price_ex_tax', 0);
		$p->manufacturer_id = Input::get('manufacturer_id');
		$p->vatgroup_id = Input::get('vatgroup');
		$p->categories = Input::get('categories', []);


		# add tabs
		for($i = 1; $i < 10; $i++) {
			if (Input::has('tab-' .$i .'-title')) {
				$title = Input::get('tab-' .$i .'-title');
				$body = Input::get('tab-' .$i .'-content');
				$id = Input::get('tab-' .$i .'-id', null);

				if ($id != null) {
					$tab = Producttab::findOrNew($i);
				}else {
					$tab = new Producttab();
				}

				$tab->title = trim($title);
				$tab->body = trim($body);
				$p->producttabs()->save($tab);
			}else {
				break;
			}
		}

		# sync variants
		$p->variants()->sync(Input::get('variants', []));

		# update variant stock?
		if (count($p->variants) > 0) {
			$stock = [];

			$rootVariant = $p->variants[0];

			if (count($p->variants) > 1) {
				foreach($rootVariant->data['values'] as $rootValue) {
					foreach($p->variants as $variant) {
						if ($rootVariant == $variant) continue;

						foreach($variant['data']['values'] as $value) {
							$stockID = $rootValue['id'] .'_' .$value['id'];
							$stock[$stockID] = Input::get('variant-' .$stockID, 0);
						}
					}
				}
			}else {
				foreach($rootVariant->data['values'] as $value) {
					$stockID = $value['id'];
					$stock[$stockID] = Input::get('variant-' .$stockID, 0);
				}
			}

			$p->variants_stock = $stock;
		}

		# save
		$p->save();

		# sync tags
		$p->tags()->sync(Input::get('tags', []));

		$queryParams = Session::get('admin_products_index_query', '');
		return Redirect::to(route('admin.products.index') .'?' .$queryParams)->with('success', 'Product updated!');
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

	public function destroyTab(Producttab $tab) {
		$tab->delete();
		return response('OK', 200);
	}

	/**
	 * "Move" several products to a given set of categories
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function batchMove() {
		$products = explode(',', Input::get('products', ''));
		$categories = Input::get('categories', []);

		sort($categories, SORT_ASC);

		DB::table('products')->whereIn('id', $products)->update([
			'categories' => implode(',', $categories),
		]);

		return Redirect::back()->with('success', count($products) .' moved!');
	}

	public function batchDestroy() {
		if ( ! Input::has('products')) return Redirect::back()->with('error', 'No products selected!');

		$products = explode(',', Input::get('products'));
		Product::whereIn('id', $products)->delete();
		return Redirect::back()->with('success', count($products) .' deleted!');
	}

	public function destroy(Product $product)
	{
		$product->delete();
		return redirect(route('admin.products.index'))->with('success', 'Product deleted.');
	}

	public function getMultiedit() {
		# get categories, manufacturers and tags
		$categories = Category::orderBy('parent_id', 'asc')->orderBy('name', 'asc')->get();
		$manufacturers = Manufacturer::orderBy('name', 'asc')->get();
		$tags = Tag::orderBy('label', 'asc')->get();
		$variants = Variant::orderBy('name', 'asc')->get();

		$columns = [];

		if (Input::has('column_name')) {
			$columns['Name'] = ['name', function($p) {
				return '<input type="text" name="' .$p->id .'_name" value="' .$p->name .'">';
			}];
		}

		if (Input::has('column_slug')) {
			$columns['Slug'] = ['slug', function($p) {
				return '<input type="text" name="' .$p->id .'_slug" value="' .$p->slug .'">';
			}];
		}

		if (Input::has('column_inprice')) {
			$columns[trans('admin.inprice')] = ['inprice', function($p) {
				return '<input type="text" name="' .$p->id .'_inprice" value="' .$p->inprice .'">';
			}];
		}

		if (Input::has('column_price')) {
			$columns[trans('admin.price')] = ['price', function($p) {
				return '<input type="text" name="' .$p->id .'_price" value="' .$p->price .'"">';
			}];
		}

		if (Input::has('column_articlenumber')) {
			$columns[trans('admin.articlenumber')] = ['articlenumber', function($p) {
				return '<input type="text" name="' .$p->id .'_articlenumber" value="' .$p->articlenumber .'">';
			}];
		}

		if (Input::has('column_barcode')) {
			$columns[trans('admin.barcode')] = ['barcode', function($p) {
				return '<input type="text" name="' .$p->id .'_barcode" value="' .$p->barcode .'">';
			}];
		}

		if (Input::has('column_weight')) {
			$columns[trans('admin.weight')] = ['weight', function($p) {
				return '<input type="text" name="' .$p->id .'_weight" value="' .$p->weight .'">';
			}];
		}

		if (Input::has('column_description')) {
			$columns[trans('admin.description')] = ['description', function($p) {
				return '<textarea class="wysiwyg" name="' .$p->id .'_description">' .$p->description .'</textarea>';
			}];
		}

		if (Input::has('column_summary')) {
			$columns[trans('admin.summary')] = ['summary', function($p) {
				return '<textarea class="wysiwyg" name="' .$p->id .'_summary">' .$p->summary .'</textarea>';
			}];
		}

		if (Input::has('column_stock')) {
			$columns[trans('admin.stock')] = ['stock', function($product) {
				$stock = $product->variants_stock;

				$str = '';

				if ( ! $stock) {
					$stock = [];
				}

				if (count($product->variants) > 0) {
					$str .= '<table>';
					$rootVariant = $product->variants[0];
					if (count($product->variants) > 1) {
						foreach($rootVariant->data['values'] as $rootValue) {
							foreach($product->variants as $variant) {
								if ($rootVariant == $variant) continue;

								foreach($variant['data']['values'] as $value) {
									$stockID = $rootValue['id'] .'_' .$value['id'];
									$s = 0;
									if (isset($stock[$stockID])) {
										$s = $stock[$stockID];
									}

									$str .= '<tr>';
									$str .= '<td>' .$rootValue['name'] .' ' .$value['name'] .'</td>';
									$str .= '<td><input type="text" name="' .$product->id .'_variant-' .$stockID .'" value="' .$s .'"></td>';
									$str .= '<tr>';
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

							$str .= '<tr>';
							$str .= '<td>' .$value['name'] .'</td>';
							$str .= '<td><input type="text" name="' .$product->id .'_variant-' .$value['id'] .'" value="' .$s .'"></td>';
							$str .= '</tr>';
						}
					}
					$str .= '</table>';
				}else {
					return '<input type="text" name="' .$product->id .'_stock" value="' .$product->stock .'">';
				}

				return $str;
			}];
		}

		if (Input::has('column_categories') || true) {
			$columns[trans('admin.categories')] = ['categories', function($p) use($categories) {
				$str = '<select class="categories" multiple="multiple" name="' .$p->id .'_categories[]">';
				foreach($categories as $cat) {
					if ($p->categories->contains($cat)) {
						$str .= '<option selected value="' .$cat->id .'">' .$cat->name .'</option>';
					}else {
						$str .= '<option value="' .$cat->id .'">' .$cat->name .'</option>';
					}
				}
				$str .= '</select>';
				return $str;
			}];
		}

		if (Input::has('column_tags')) {
			$columns[trans('admin.tags')] = ['tags', function($p) use($tags) {
				$str = '<select class="tags" multiple name="' .$p->id .'_tags[]">';
				foreach($tags as $tag) {
					if ($p->tags->contains($tag)) {
						$str .= '<option selected value="' .$tag->id .'">' .$tag->label .'</option>';
					}else {
						$str .= '<option value="' .$tag->id .'">' .$tag->label .'</option>';
					}
				}
				$str .= '</select>';
				return $str;
			}];
		}

		if (Input::has('column_variants')) {
			$columns[trans('admin.variants')] = ['variants', function($p) use($variants) {
				$str = '<select class="variants" multiple name="' .$p->id .'_variants[]">';
				foreach($variants as $variant) {
					if ($p->variants->contains($variant)) {
						$str .= '<option selected value="' .$variant->id .'">' .$variant->admin_name .'</option>';
					}else {
						$str .= '<option value="' .$variant->id .'">' .$variant->admin_name .'</option>';
					}
				}
				$str .= '</select>';
				return $str;
			}];
		}

		if (Input::has('column_manufacturer') || true) {
			$columns[trans('admin.manufacturer')] = ['manufacturer_id', function($p) use($manufacturers) {
				$str = '<select class="manufacturer" name="' .$p->id .'_manufacturer">';
				foreach($manufacturers as $mf) {
					if ($mf == $p->manufacturer) {
						$str .= '<option selected value="' .$mf->id .'">' .$mf->name .'</option>';
					}else {
						$str .= '<option value="' .$mf->id .'">' .$mf->name .'</option>';
					}
				}
				$str .= '</select>';
				return $str;
			}];
		}

		$columns[trans('admin.enabled')] = ['enabled', function($p) {
			$str = '<label class="checkbox-container" for="' .$p->id .'_enabled" style="float: left; margin-right: 1rem;">' .trans('admin.enabled') .'
				<div class="checkbox">';
			if ($p->enabled) {
				$str .= '<input type="hidden" name="' .$p->id .'_enabled" value="off">';
				$str .= '<input type="checkbox" checked="checked" id="' .$p->id .'_enabled" name="' .$p->id .'_enabled">';
			}else {
				$str .= '<input type="hidden" name="' .$p->id .'_enabled" value="off">';
				$str .= '<input type="checkbox" id="' .$p->id .'_enabled" name="' .$p->id .'_enabled">';
			}
			$str .= '<span></span></div></label>';
			return $str;
		}];

		$table = Laratable::make(Product::query(), $columns);

		$table->sortable(true, [
			'name','inprice','price','stock','enabled', 'categories','manufacturer_id',
		]);

		$table->filterable(true);

		$mfs = ['*' => 'Any'];
		foreach($manufacturers as $mf) {
			$mfs[$mf->id] = $mf->name;
		}
		$table->addFilter('manufacturer_id', 'select')->setValues($mfs);

		$table->addFilter('categories', 'category')->setLabel("Categories");

		$enabledColumns = [];
		$table->appendQueryParams(Input::only('column_name', 'column_slug', 'column_inprice', 'column_price', 'column_articlenumber', 'column_barcode', 'clumn_weight', 'column_description', 'column_summary', 'column_stock', 'column_tags', 'column_variants'));

		$table->paginate(true, 30);

		return $this->view('admin.products_multiedit')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
				'filters' => $table->buildFilters()->addClass('inline')->filters,
				'categories' => $categories,
				'manufacturers' => $manufacturers,
				'tags' => $tags,
				'columns' => implode(',', array_column($columns, 0)),
			]);
	}

	public function putMultiedit() {
		$cols = [
			'name' => '',
			'slug' => '',
			'inprice' => 0,
			'price' => 0,
			'articlenumber' => 0,
			'barcode' => 0,
			'weight' => 0,
			'description' => '',
			'summary' => '',
			'enabled' => false,
			'manufacturer_id' => null,
			'stock' => 0
		];

		$columns = explode(',', Input::get('columns', ''));

		foreach(Product::all() as $p) {
			$id = $p->id;

			# are we editing this one?
			if ( ! Input::has($id .'_enabled')) continue;

			foreach($cols as $col => $default) {
				if (Input::has($id .'_' .$col) || in_array($col, $columns)) {
					$p->{$col} = Input::get($id .'_' .$col, $default);
				}
			}

			if (Input::has($id .'_categories')) {
				$p->categories = Input::get($id .'_categories', []);
			}

			if (Input::has($id .'_tags')) {
				$p->tags()->sync(Input::get($id .'_tags', []));
			}

			if (Input::has($id .'_variants')) {
				$p->variants()->sync(Input::get($id .'_variants', []));
			}

			$p->enabled = (Input::get($id .'_enabled', 'off') == 'on') ? true : false;

			# update variant stock?
			if (count($p->variants) > 0 && in_array('stock', $columns)) {
				$stock = [];

				$rootVariant = $p->variants[0];

				if (count($p->variants) > 1) {
					foreach($rootVariant->data['values'] as $rootValue) {
						foreach($p->variants as $variant) {
							if ($rootVariant == $variant) continue;

							foreach($variant['data']['values'] as $value) {
								$stockID = $rootValue['id'] .'_' .$value['id'];
								if (Input::has($id .'_variant-' .$stockID)) {
									$stock[$stockID] = Input::get($id .'_variant-' .$stockID, 0);
								}
							}
						}
					}
				}else {
					foreach($rootVariant->data['values'] as $value) {
						$stockID = $value['id'];
						if (Input::has($id .'_variant-' .$stockID)) {
							$stock[$stockID] = Input::get($id .'_variant-' . $stockID, 0);
						}
					}
				}

				$p->variants_stock = $stock;
			}

			$p->save();
		}

		return Redirect::back()->with('success', 'Products Updated!');
	}

}