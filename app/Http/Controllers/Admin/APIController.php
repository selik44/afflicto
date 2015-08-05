<?php namespace Friluft\Http\Controllers\Admin;

use DB;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Image;
use Friluft\Product;
use Friluft\Variant;
use Input;

class APIController extends Controller {

	public function products_setEnabled(Product $p) {
		$p->enabled = Input::get('enabled');
		$p->save();
		return response('OK');
	}

	public function products_getCategories(Product $p) {
		$cats = [];
		foreach(Category::all() as $category) {
			$array = $category->toArray();
			$array['selected'] = $p->categories->contains($category);
			$cats[] = $array;
		}

		return $cats;
	}

	public function products_syncCategories(Product $p) {
		$p->categories()->sync(Input::get('categories', []));
		return response('OK');
	}

	public function products_postImage(Product $p) {
		# get the uploaded file
		$file = Input::file('file');

		# create a new image instance
		$image = new Image();

		$image->type = 'product';

		# save, to get ID
		$p->images()->save($image);

		# set name and save again
		$image->name = 'product_' .$p->id .'_' .$image->id .'.' .$file->getClientOriginalExtension();

		# move it to the public dir
		if ($file->move(public_path('images/products/'), $image->name)) {
			$image->save();
			return response('OK', 200);
		}

		$image->delete();

		return response('ERROR', 500);
	}

	public function products_setImageOrder(Product $p) {
		if (!Input::has('order')) return response('ERROR: Invalid input.', 400);
		$order = json_decode(Input::get('order'), true);
		foreach($order as $image) {
			DB::table('images')
				->where('id', '=', $image['id'])
				->update(['order' => $image['order']]);
		}
		return response('OK', 200);
	}

	public function products_destroyImage(Product $p) {
		$id = Input::get('id');
		DB::table('images')->delete($id);

		return response('OK', 200);
	}

	public function products_addVariant(Product $p) {
		$variant = new Variant(Input::only('name'));
		$data = ['values' => []];

		# set values array
		$values = Input::get('values');
		$values = trim($values, '\r\n\t, ');
		$values = explode(',', $values);

		# loop through values array and add stock, name etc.
		foreach($values as $value) {
			$data['values'][$value] = ['name' => $value, 'stock' => 0];
		}

		$variant->data = $data;

		$p->variants()->save($variant);
		return response('OK');
	}

	public function products_updateVariant(Product $p, Variant $v) {
		# get data
		$data = $v->data;

		# set values array
		$values = Input::get('values');
		$values = trim($values, '\r\n\t, ');
		$values = explode(',', $values);

		# loop through values array and add stock, name etc.
		foreach($values as $value) {
			$data['values'][$value] = ['name' => $value, 'stock' => 0];
		}

		$v->data = $data;

		# save
		$v->save();
		return response('OK');
	}

	public function products_setVariantsStock(Product $p, Variant $v) {
		$data = $v->data;
		$data['values'][Input::get('value')]['stock'] = Input::get('stock');

		$v->data = $data;
		$v->save();

		return response('OK');
	}

	public function products_removeVariant(Product $p, Variant $v) {
		$p->variants()->detach($v);
		return response('OK');
	}

}
