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

Route::group(['prefix' => $locale], function() {

	# home
	Route::get('/', ['as' => '/', 'uses' => 'HomeController@index']);
	Route::get('home', ['as' => 'home', 'uses' => 'HomeController@index']);

	# terms & conditions
	Route::get('terms-and-conditions', 'HomeController@terms');

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
	#Route::post('store/checkout', ['as' => 'store.checkout.order', 'uses' => 'StoreController@order']);
	Route::get('store/success', ['as' => 'store.checkout.success', 'uses' => 'StoreController@success']);
	Route::post('store/push', ['as' => 'store.checkout.push', 'uses' => 'StoreController@push']);
	Route::get('store/{path}', ['as' => 'store', 'uses' => 'StoreController@index'])->where('path', '[a-z0-9/-]+');

	# search
	Route::get('search', ['as' => 'search', 'uses' => 'SearchController@index']);

	# cart API
	Route::resource('cart', 'CartController');

	# html
	Route::get('html/product/{product}', ['as' => 'html.product', function($product) {
		return view('front.partial.product_modal')->with('product', $product);
	}]);

	
	/*---------------------------
	*	Admin routes
	*--------------------------*/
	Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function() {
		# dashboard
		Route::get('/', ['as' => 'admin', 'uses' => 'Admin\DashboardController@index']);
		Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@index']);

		# products
		Route::resource('products', 'Admin\ProductsController');

		# categories
		Route::get('categories/tree', ['as' => 'admin.categories.tree', 'uses' => 'Admin\CategoriesController@tree']);
		Route::put('categories/tree', ['as' => 'admin.categories.tree_update', 'uses' => 'Admin\CategoriesController@tree_update']);
		Route::resource('categories', 'Admin\CategoriesController');
	});
});