<?php

use Friluft\Category;
use Friluft\Product;

$languages = ['en', 'no', 'se'];

$locale = Request::segment(1);

if (in_array($locale, $languages)) {
	App::setLocale($locale);
}else {
	$locale = '';
}


Route::get('/', function() {
	return 'hello world';
});

Route::group(['prefix' => $locale], function() {

	# home
	Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

		
	# auth
	Route::controllers([
		'auth' => 'Auth\AuthController',
		'password' => 'Auth\PasswordController',
	]);


	# search
	Route::get('search', ['as' => 'search', function() {

		if (Input::has('terms')) {
			return view('front.search')
				->with([
					'products' => Product::enabled()->search(Input::get('terms'))->get(),
					'aside' => true
				]);
		}

		return redirect('search')->with('error', 'A search term is required!');
	}]);


	Route::get('html/product/{product}', ['as' => 'html.product', function($product) {
		return view('front.partial.product_modal')->with('product', $product);
	}]);


	# store
	Route::get('store/{path}', ['as' => 'store', function($path) {
		$path = explode('/', $path);
		$slug = array_pop($path);

		$cat = Category::where('slug', '=', $slug)->first();

		if ($cat) {
			return view('front.store_category')->with('category', $cat)->with('aside', true);
		}

		$product = Product::where('slug', '=', $slug)->first();

		if ($product) {
			$slug = array_pop($path);
			
			if ($slug) {
				$category = Category::where('slug', '=', $slug)->first();
			}

			return view('front.store_product')
				->with([
					'category' => $category,
					'product' => $product,
					'aside' => true
				]);
		}

		abort(404);

	}])->where('path', '[a-z0-9/-]+');




	/*---------------------------
	*	Admin routes
	*--------------------------*/
	Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function() {

		Route::get('/', ['as' => 'admin', function() {
			return redirect('admin/dashboard');
		}]);

		Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@index']);

		# Products
		Route::get('products/show/{id}', ['as' => 'admin.products.show', 'uses' => 'Admin\ProductsController@show']);
		Route::get('products/create', ['as' => 'admin.products.create', 'uses' => 'Admin\ProductsController@create']);
		Route::post('products', ['as' => 'admin.products.store', 'uses' => 'Admin\ProductsController@store']);
		Route::get('products/destroy/{id}', ['as' => 'admin.products.destroy', 'uses' => 'Admin\ProductsController@destroy']);
		Route::get('products/{page?}/{column?}/{direction?}', ['as' => 'admin.products', 'uses' => 'Admin\ProductsController@index']);

		# Categories
		Route::get('categories/tree', ['as' => 'admin.categories.tree', 'uses' => 'Admin\CategoriesController@tree']);
		Route::put('categories/tree', ['as' => 'admin.categories.tree_update', 'uses' => 'Admin\CategoriesController@tree_update']);

		

		Route::get('categories/show/{id}', ['as' => 'admin.categories.show', 'uses' => 'Admin\CategoriesController@show']);
		Route::get('categories/create', ['as' => 'admin.categories.create', 'uses' => 'Admin\CategoriesController@create']);
		Route::post('categories', ['as' => 'admin.categories.store', 'uses' => 'Admin\CategoriesController@store']);
		Route::get('categories/destroy/{id}', ['as' => 'admin.categories.destroy', 'uses' => 'Admin\CategoriesController@destroy']);
		Route::get('categories/{page?}/{column?}/{direction?}', ['as' => 'admin.categories', 'uses' => 'Admin\CategoriesController@index']);
		
	});
});