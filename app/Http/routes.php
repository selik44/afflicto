<?php

use Friluft\Category;
use Friluft\Product;
use Friluft\PDF\PDF;
use Friluft\Variant;

$languages = ['en', 'no', 'se'];

$locale = Request::segment(1);

if (in_array($locale, $languages)) {
	App::setLocale($locale);
}else {
	$locale = '';
}

Route::get('test', function() {
	$pdf = new PDF('/usr/bin/wkhtmltopdf');

	$pdf->loadHTML(view('front.home')->render());
	echo $pdf->grayscale()->pageSize('A3')->orientation('Landscape')->get();

	#echo $pdf->loadURL('http://google.com')->grayscale()->pageSize('A3')->orientation('Landscape')->get();
});

Route::group(['prefix' => $locale], function() {

	# home
	Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
	Route::get('terms-and-conditions', ['as' => 'home.terms', 'uses' => 'HomeController@terms']);

	# auth
	Route::group(['prefix' => 'user'], function() {
		# login
		Route::get('login', ['as' => 'user.login', 'uses' => 'AuthController@get_login']);
		Route::post('login', ['as' => 'user.login.post', 'uses' => 'AuthController@post_login']);

		# logout
		Route::get('logout', ['as' => 'user.logout', 'uses' => 'AuthController@get_logout']);

		# register
		Route::get('register', ['as' => 'user.register', 'uses' => 'AuthController@get_register']);
		Route::post('register', ['as' => 'user.register.post', 'uses' => 'AuthController@post_register']);

		# forgot
		Route::get('forgot', ['as' => 'user.forgot', 'uses' => 'AuthController@get_forgot']);
		Route::post('forgot', ['as' => 'user.forgot.post', 'uses' => 'AuthController@post_forgot']);

		Route::get('reset/{token}', ['as' => 'user.reset', 'uses' => 'AuthController@get_reset']);
		Route::post('reset', ['as' => 'user.reset.post', 'uses' => 'AuthController@post_reset']);
	});

	# store
	Route::get('store/cart', ['as' => 'store.cart', 'uses' => 'StoreController@cart']);
	Route::get('store/checkout', ['as' => 'store.checkout', 'uses' => 'StoreController@checkout']);
	Route::post('store/checkout', ['as' => 'store.checkout.order', 'uses' => 'StoreController@order']);
	Route::get('store/success', ['as' => 'store.checkout.success', 'uses' => 'StoreController@success']);
	Route::post('store/push', ['as' => 'store.checkout.push', 'uses' => 'StoreController@push']);
	Route::get('store/{path}', ['as' => 'store', 'uses' => 'StoreController@index'])->where('path', '[a-z0-9/-]+');

	# search
	Route::get('search', ['as' => 'search', 'uses' => 'SearchController@index']);

	# cart API
	Route::resource('cart', 'CartController');
	Route::get('cart', ['as' => 'cart.index', 'uses' => 'CartController@index']);
	Route::get('cart/{cart}', ['as' => 'cart.show', 'uses' => 'CartController@show']);
	Route::get('cart/create', ['as' => 'cart.create', 'uses' => 'CartController@create']);
	Route::post('cart/create', ['as' => 'cart.store', 'uses' => 'CartController@store']);
	Route::get('cart/{cart}/edit', ['as' => 'cart.edit', 'uses' => 'CartController@edit']);
	Route::put('cart/{cart}', ['as' => 'cart.update', 'uses' => 'CartController@update']);

	# html/ajax API
	Route::get('html/product/{product}', ['as' => 'html.product', function($product) {
		return view('front.partial.product_modal')->with('product', $product);
	}]);

	/*---------------------------
	*	Admin routes
	*--------------------------*/
	Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function() {
		# api
		Route::put('api/products/{product}/setenabled', function(Product $p) {
			$p->enabled = Input::get('enabled');
			$p->save();
			return response('OK');
		});

		# get categories
		Route::get('api/products/{product}/categories', function(Product $p) {
			$cats = [];
			foreach(Category::all() as $category) {
				$array = $category->toArray();
				$array['selected'] = $p->categories->contains($category);
				$cats[] = $array;
			}

			return $cats;
		});

		# sync categories
		Route::post('api/products/{product}/categories', function(Product $p) {
			$p->categories()->sync(Input::get('categories', []));
			return response('OK');
		});

		# add image
		Route::post('api/products/{product}/images', function(Product $p) {
			$images = $p->images;

			$file = Input::file('file');
			$name = 'product_' .$p->id .'_' .count($images) .'.' .$file->getClientOriginalExtension();

			if ($file->move(public_path('images/products/'), $name)) {
				$images[] = ['order' => 0, 'image' => $name];

				$p->images = $images;
				$p->save();
				return response('OK', 200);
			}

			return response('ERROR', 500);
		});

		# add variant
		Route::post('api/products/{product}/variants', function(Product $p) {
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
		});

		# update variant
		Route::put('api/products/{product}/variants/{variant}', function(Product $p, Variant $v) {
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
		});

		# update variant stock
		Route::put('api/products/{product}/variants/{variant}/setstock', function(Product $p, Variant $v) {
			$data = $v->data;
			$data['values'][Input::get('value')]['stock'] = Input::get('stock');

			$v->data = $data;
			$v->save();

			return response('OK');
		});

		# remove variant
		Route::delete('api/products/{product}/variants/{variant}', function(Product $product, Variant $variant) {
			$variant->delete();
			return response('OK');
		});

		Route::get('html/category/{category}/products', ['as' => 'html.category.products', function(Category $category) {
			return view('admin.partial.products_list')
				->with([
					'category' => $category
				]);
		}]);

		# dashboard
		Route::get('/', ['as' => 'admin', 'uses' => 'Admin\DashboardController@index']);
		Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@index']);

		# users
		Route::get('users', ['as' => 'admin.users.index', 'uses' => 'Admin\UsersController@index']);
		Route::get('users/create', ['as' => 'admin.users.create', 'uses' => 'Admin\UsersController@create']);
		Route::post('users', ['as' => 'admin.users.store', 'uses' => 'Admin\UsersController@store']);
		Route::get('users/{user}/edit', ['as' => 'admin.users.edit', 'uses' => 'Admin\UsersController@edit']);
		Route::get('users/{user}', ['as' => 'admin.users.show', 'uses' => 'Admin\UsersController@show']);
		Route::put('users/{user}', ['as' => 'admin.users.update', 'uses' => 'Admin\UsersController@update']);
		Route::delete('users/{user}', ['as' => 'admin.users.delete', 'uses' => 'Admin\UsersController@destroy']);

		# roles
		Route::get('roles', ['as' => 'admin.roles.index', 'uses' => 'Admin\RolesController@index']);
		Route::post('roles', ['as' => 'admin.roles.store', 'uses' => 'Admin\RolesController@store']);
		Route::get('roles/create', ['as' => 'admin.roles.create', 'uses' => 'Admin\RolesController@create']);
		Route::get('roles/{role}/edit', ['as' => 'admin.roles.edit', 'uses' => 'Admin\RolesController@edit']);
		Route::get('roles/{role}', ['as' => 'admin.roles.show', 'uses' => 'Admin\RolesController@show']);
		Route::put('roles/{role}', ['as' => 'admin.roles.update', 'uses' => 'Admin\RolesController@update']);
		Route::delete('roles/{role}', ['as' => 'admin.roles.delete', 'uses' => 'Admin\RolesController@destroy']);

		# products
		Route::get('products', ['as' => 'admin.products.index', 'uses' => 'Admin\ProductsController@index']);
		Route::get('products/create', ['as' => 'admin.products.create', 'uses' => 'Admin\ProductsController@create']);
		Route::get('products/{product}/edit', ['as' => 'admin.products.edit', 'uses' => 'Admin\ProductsController@edit']);
		Route::get('products/{product}', ['as' => 'admin.products.show', 'uses' => 'Admin\ProductsController@show']);
		Route::put('products/{product}', ['as' => 'admin.products.update', 'uses' => 'Admin\ProductsController@update']);
		Route::post('products', ['as' => 'admin.products.store', 'uses' => 'Admin\ProductsController@store']);
		Route::delete('products/{product}', ['as' => 'admin.products.delete', 'uses' => 'Admin\ProductsController@destroy']);

		# categories
		Route::get('categories/tree', ['as' => 'admin.categories.tree', 'uses' => 'Admin\CategoriesController@tree']);
		Route::put('categories/tree', ['as' => 'admin.categories.tree.update', 'uses' => 'Admin\CategoriesController@tree_update']);

		Route::get('categories', ['as' => 'admin.categories.index', 'uses' => 'Admin\CategoriesController@index']);
		Route::get('categories/create', ['as' => 'admin.categories.create', 'uses' => 'Admin\CategoriesController@create']);
		Route::get('categories/{category}/edit', ['as' => 'admin.categories.edit', 'uses' => 'Admin\CategoriesController@edit']);
		Route::get('categories/{category}', ['as' => 'admin.categories.show', 'uses' => 'Admin\CategoriesController@show']);
		Route::put('categories/{category}', ['as' => 'admin.categories.update', 'uses' => 'Admin\CategoriesController@update']);
		Route::post('categories', ['as' => 'admin.categories.store', 'uses' => 'Admin\CategoriesController@store']);

		# manufacturers
		Route::get('manufacturers', ['as' => 'admin.manufacturers.index', 'uses' => 'Admin\ManufacturersController@index']);
		Route::get('manufacturers/create', ['as' => 'admin.manufacturers.create', 'uses' => 'Admin\ManufacturersController@create']);
		Route::get('manufacturers/{manufacturer}/edit', ['as' => 'admin.manufacturers.edit', 'uses' => 'Admin\ManufacturersController@edit']);
		Route::get('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.show', 'uses' => 'Admin\ManufacturersController@show']);
		Route::put('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.update', 'uses' => 'Admin\ManufacturersController@update']);
		Route::post('manufacturers', ['as' => 'admin.manufacturers.store', 'uses' => 'Admin\ManufacturersController@store']);
		Route::delete('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.delete', 'uses' => 'Admin\ManufacturersController@destroy']);

		# orders
		Route::get('orders', ['as' => 'admin.orders.index', 'uses' => 'Admin\OrdersController@index']);
		Route::get('orders/create', ['as' => 'admin.orders.create', 'uses' => 'Admin\OrdersController@create']);
		Route::get('orders/{manufacturer}/edit', ['as' => 'admin.orders.edit', 'uses' => 'Admin\OrdersController@edit']);
		Route::get('orders/{manufacturer}', ['as' => 'admin.orders.show', 'uses' => 'Admin\OrdersController@show']);
		Route::put('orders/{manufacturer}', ['as' => 'admin.orders.update', 'uses' => 'Admin\OrdersController@update']);
		Route::post('orders', ['as' => 'admin.orders.store', 'uses' => 'Admin\OrdersController@store']);
		Route::delete('orders/{order', ['as' => 'admin.orders.delete', 'uses' => 'Admin\OrdersController@destroy']);
	});
});