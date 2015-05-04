<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Friluft\Category;
use Friluft\Product;

#Session::flash("success", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rem, doloribus!");

/*---------------------------
*	Front routes
*--------------------------*/
Route::get('/', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);



/*---------------------------
*	Search route
*--------------------------*/
Route::get('search', function() {

	if (Input::has('terms')) {
		return view('front.search')
			->with([
				'products' => Product::enabled()->search(Input::get('terms'))->get(),
				'aside' => true
			]);
	}

	return redirect('search')->with('error', 'A search term is required!');
});



Route::get('html/product/{product}', function($product) {
	return view('front.partial.product_modal')->with('product', $product);
});



/*---------------------------
*	Store
*--------------------------*/
Route::get('store/{path}', function($path) {
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

})->where('path', '[a-z0-9/-]+');




/*---------------------------
*	Admin routes
*--------------------------*/
Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function() {

	Route::get('/', function() {
		return redirect('admin/dashboard');
	});

	Route::get('dashboard', 'Admin\DashboardController@index');

	# Products
	Route::get('products/{page?}/{column?}/{direction?}', 'Admin\ProductsController@index');
	Route::get('products/show/{id}', 'Admin\ProductsController@show');
	Route::get('products/create', 'Admin\ProductsController@create');
	Route::post('products', 'Admin\ProductsController@store');
	Route::get('products/destroy/{id}', 'Admin\ProductsController@destroy');

	# Categories
	Route::get('categories/{page?}/{column?}/{direction?}', 'Admin\CategoriesController@index');
	Route::get('categories/show/{id}', 'Admin\CategoriesController@show');
	Route::get('categories/create', 'Admin\CategoriesController@create');
	Route::post('categories', 'Admin\CategoriesController@store');
	Route::get('categories/destroy/{id}', 'Admin\CategoriesController@destroy');
	
});