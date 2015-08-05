<?php

# HOME ROUTES
get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
get('terms-and-conditions', ['as' => 'home.terms', 'uses' => 'HomeController@terms']);
get('search', ['as' => 'search', 'uses' => 'SearchController@index']);


# AUTH & USER ROUTES
Route::group(['prefix' => 'user'], function() {
	# login
	get('login', ['as' => 'user.login', 'uses' => 'AuthController@get_login']);
	post('login', ['as' => 'user.login.post', 'uses' => 'AuthController@post_login']);

	# logout
	get('logout', ['as' => 'user.logout', 'uses' => 'AuthController@get_logout']);

	# register
	get('register', ['as' => 'user.register', 'uses' => 'AuthController@get_register']);
	post('register', ['as' => 'user.register.post', 'uses' => 'AuthController@post_register']);

	# forgot
	get('forgot', ['as' => 'user.forgot', 'uses' => 'AuthController@get_forgot']);
	post('forgot', ['as' => 'user.forgot.post', 'uses' => 'AuthController@post_forgot']);

	# reset
	get('reset/{token}', ['as' => 'user.reset', 'uses' => 'AuthController@get_reset']);
	post('reset', ['as' => 'user.reset.post', 'uses' => 'AuthController@post_reset']);

	# USER DASHBOARD
	Route::group(['middleware' => 'auth'], function() {
		get('/', ['as' => 'user', 'uses' => 'UserController@index']);
		get('orders', ['as' => 'user.orders', 'uses' => 'UserController@getOrders']);
		get('order/{order}', ['as' => 'user.order', 'uses' => 'UserController@getOrder']);
		get('settings', ['as' => 'user.settings', 'uses' => 'UserController@getSettings']);
		put('settings', ['as' => 'user.settings.save', 'uses' => 'UserController@putSettings']);
	});
});


# API ROUTES
get('api/cart', ['as' => 'api.cart.index', 'uses' => 'CartController@index']);
get('api/cart/clear', ['as' => 'api.cart.clear', 'uses' => 'CartController@clear']);
get('api/cart/{cart}', ['as' => 'api.cart.show', 'uses' => 'CartController@show']);
post('api/cart', ['as' => 'api.cart.store', 'uses' => 'CartController@store']);
put('api/cart/{id}/quantity', ['as' => 'api.cart.quantity', 'uses' => 'CartController@setQuantity']);
delete('api/cart/{id}', ['as' => 'api.cart.destroy', 'uses' => 'CartController@destroy']);

get('api/proteria/update', ['middleware' => 'auth.basic', 'as' => 'api.proteria.update', 'uses' => 'Admin\ProteriaController@update']);
get('api/proteria/orders', ['middleware' => 'auth.basic', 'as' => 'admin.proteria.export', 'uses' => 'Admin\ProteriaController@getExport']);


# ADMIN ROUTES
Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function() {

	# API
	Route::group(['middleware' => 'admin', 'prefix' => 'api'], function() {
		# enable/disable product
		put('products/{product}/setenabled', ['as' => 'admin.api.products.setEnabled', 'uses' => 'Admin\APIController@products_setEnabled']);

		# get categories for product
		get('products/{product}/categories', ['as' => 'admin.api.products.getCategories', 'uses' => 'Admin\APIController@products_getCategories']);

		# sync categories
		post('products/{product}/categories', ['as' => 'admin.api.products.syncCategories', 'uses' => 'Admin\APIController@products_syncCategories']);

		# add product image
		post('products/{product}/images', ['as' => 'admin.api.products.postImage', 'uses' => 'Admin\APIController@products_postImage']);

		# update image order
		put('products/{product}/images/order', ['as' => 'admin.api.products.setImageOrder', 'uses' => 'Admin\APIController@products_setImageOrder']);

		# delete product image
		delete('products/{product}/images', ['as' => 'admin.api.products.destroyImage', 'uses' => 'Admin\APIController@products_destroyImage']);

		# add variant
		post('products/{product}/variants', ['as' => 'admin.api.products.addVariant', 'uses' => 'Admin\APIController@products_addVariant']);

		# update variant
		put('products/{product}/variants/{variant}', ['as' => 'admin.api.products.updateVariant', 'uses' => 'Admin\APIController@products_updateVariant']);

		# update variant stock
		put('products/{product}/variants/{variant}/setstock', ['as' => 'admin.api.products.setVariantsStock', 'uses' => 'Admin\APIController@products_setVariantsStock']);

		# remove variant
		delete('products/{product}/variants/{variant}', ['as' => 'admin.api.products.removeVariant', 'uses' => 'Admin\APIController@products_removeVariant']);
	});

	# dashboard
	get('/', ['as' => 'admin', 'uses' => 'Admin\DashboardController@index']);
	get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@index']);

	# users
	get('users', ['as' => 'admin.users.index', 'uses' => 'Admin\UsersController@index']);
	get('users/create', ['as' => 'admin.users.create', 'uses' => 'Admin\UsersController@create']);
	post('users', ['as' => 'admin.users.store', 'uses' => 'Admin\UsersController@store']);
	get('users/{user}/edit', ['as' => 'admin.users.edit', 'uses' => 'Admin\UsersController@edit']);
	get('users/{user}', ['as' => 'admin.users.show', 'uses' => 'Admin\UsersController@show']);
	put('users/{user}', ['as' => 'admin.users.update', 'uses' => 'Admin\UsersController@update']);
	delete('users/{user}', ['as' => 'admin.users.delete', 'uses' => 'Admin\UsersController@destroy']);

	# roles
	get('roles', ['as' => 'admin.roles.index', 'uses' => 'Admin\RolesController@index']);
	post('roles', ['as' => 'admin.roles.store', 'uses' => 'Admin\RolesController@store']);
	get('roles/create', ['as' => 'admin.roles.create', 'uses' => 'Admin\RolesController@create']);
	get('roles/{role}/edit', ['as' => 'admin.roles.edit', 'uses' => 'Admin\RolesController@edit']);
	get('roles/{role}', ['as' => 'admin.roles.show', 'uses' => 'Admin\RolesController@show']);
	put('roles/{role}', ['as' => 'admin.roles.update', 'uses' => 'Admin\RolesController@update']);
	delete('roles/{role}', ['as' => 'admin.roles.delete', 'uses' => 'Admin\RolesController@destroy']);

	# products
	delete('products/batch', ['as' => 'admin.products.batch.destroy', 'uses' => 'Admin\ProductsController@batchDestroy']);
	put('products/batch/move', ['as' => 'admin.products.batch.move', 'uses' => 'Admin\ProductsController@batchMove']);

	get('products/quick-edit', ['as' => 'admin.products.quick-edit', 'uses' => 'Admin\ProductsController@getMultiedit']);
	put('products/quick-edit', ['as' => 'admin.products.quick-edit.save', 'uses' => 'Admin\ProductsController@putMultiedit']);

	get('products', ['as' => 'admin.products.index', 'uses' => 'Admin\ProductsController@index']);
	get('products/create', ['as' => 'admin.products.create', 'uses' => 'Admin\ProductsController@create']);
	get('products/{product}/edit', ['as' => 'admin.products.edit', 'uses' => 'Admin\ProductsController@edit']);
	get('products/{product}', ['as' => 'admin.products.show', 'uses' => 'Admin\ProductsController@show']);
	put('products/{product}', ['as' => 'admin.products.update', 'uses' => 'Admin\ProductsController@update']);
	put('products/{product}/relate/{related}', ['as' => 'admin.products.relate', 'uses' => 'Admin\ProductsController@relate']);
	put('products/{product}/unrelate/{related}', ['as' => 'admin.products.unrelate', 'uses' => 'Admin\ProductsController@unrelate']);
	post('products', ['as' => 'admin.products.store', 'uses' => 'Admin\ProductsController@store']);
	delete('products/{product}', ['as' => 'admin.products.delete', 'uses' => 'Admin\ProductsController@destroy']);

	# variants
	get('variants', ['as' => 'admin.variants.index', 'uses' => 'Admin\VariantsController@index']);
	get('variants/create', ['as' => 'admin.variants.create', 'uses' => 'Admin\VariantsController@create']);
	get('variants/{variant}/edit', ['as' => 'admin.variants.edit', 'uses' => 'Admin\VariantsController@edit']);
	put('variants/{variant}', ['as' => 'admin.variants.update', 'uses' => 'Admin\VariantsController@update']);
	post('variants', ['as' => 'admin.variants.store', 'uses' => 'Admin\VariantsController@store']);
	delete('variants/{variant}', ['as' => 'admin.variants.destroy', 'uses' => 'Admin\VariantsController@destroy']);

	# categories
	get('categories/tree', ['as' => 'admin.categories.tree', 'uses' => 'Admin\CategoriesController@tree']);
	put('categories/tree', ['as' => 'admin.categories.tree.update', 'uses' => 'Admin\CategoriesController@tree_update']);

	get('categories/tree/{category}', ['as' => 'html.category.products', 'uses' => 'Admin\CategoriesController@tree_getProducts']);

	get('categories', ['as' => 'admin.categories.index', 'uses' => 'Admin\CategoriesController@index']);
	get('categories/create', ['as' => 'admin.categories.create', 'uses' => 'Admin\CategoriesController@create']);
	get('categories/{category}/edit', ['as' => 'admin.categories.edit', 'uses' => 'Admin\CategoriesController@edit']);
	get('categories/{category}', ['as' => 'admin.categories.show', 'uses' => 'Admin\CategoriesController@show']);
	put('categories/{category}', ['as' => 'admin.categories.update', 'uses' => 'Admin\CategoriesController@update']);
	post('categories', ['as' => 'admin.categories.store', 'uses' => 'Admin\CategoriesController@store']);
	delete('categories/{category}', ['as' => 'admin.categories.destroy', 'uses' => 'Admin\CategoriesController@destroy']);

	# tags
	get('tags', ['as' => 'admin.tags.index', 'uses' => 'Admin\TagsController@index']);
	get('tags/create', ['as' => 'admin.tags.create', 'uses' => 'Admin\TagsController@create']);
	get('tags/{tag}/edit', ['as' => 'admin.tags.edit', 'uses' => 'Admin\TagsController@edit']);
	put('tags/{tag}', ['as' => 'admin.tags.update', 'uses' => 'Admin\TagsController@update']);
	post('tags', ['as' => 'admin.tags.store', 'uses' => 'Admin\TagsController@store']);
	delete('tags/{tag}', ['as' => 'admin.tags.destroy', 'uses' => 'Admin\TagsController@destroy']);

	# manufacturers
	get('manufacturers', ['as' => 'admin.manufacturers.index', 'uses' => 'Admin\ManufacturersController@index']);
	get('manufacturers/create', ['as' => 'admin.manufacturers.create', 'uses' => 'Admin\ManufacturersController@create']);
	get('manufacturers/{manufacturer}/edit', ['as' => 'admin.manufacturers.edit', 'uses' => 'Admin\ManufacturersController@edit']);
	get('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.show', 'uses' => 'Admin\ManufacturersController@show']);
	put('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.update', 'uses' => 'Admin\ManufacturersController@update']);
	post('manufacturers', ['as' => 'admin.manufacturers.store', 'uses' => 'Admin\ManufacturersController@store']);
	delete('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.delete', 'uses' => 'Admin\ManufacturersController@destroy']);

	# orders
	get('orders', ['as' => 'admin.orders.index', 'uses' => 'Admin\OrdersController@index']);
	get('orders/create', ['as' => 'admin.orders.create', 'uses' => 'Admin\OrdersController@create']);
	get('orders/{order}/edit', ['as' => 'admin.orders.edit', 'uses' => 'Admin\OrdersController@edit']);
	put('orders/{order}', ['as' => 'admin.orders.update', 'uses' => 'Admin\OrdersController@update']);
	post('orders', ['as' => 'admin.orders.store', 'uses' => 'Admin\OrdersController@store']);
	delete('orders/{order}', ['as' => 'admin.orders.delete', 'uses' => 'Admin\OrdersController@destroy']);

	get('orders/{order}/edit/products', ['as' => 'admin.orders.edit.products', 'uses' => 'Admin\OrdersController@products_edit']);
	put('orders/{order}/edit/products', ['as' => 'admin.orders.edit.products.update', ['uses' => 'Admin\OrdersController@products_update']]);

	get('orders/{order}/packlist', ['as' => 'admin.orders.packlist', 'uses' => 'Admin\OrdersController@packlist']);
	get('orders/packlist/{orders}', ['as' => 'admin.orders.multipacklist', 'uses' => 'Admin\OrdersController@getMultiPacklist'])
		->where('orders', '.+');

	# proteria
	get('proteria', ['as' => 'admin.proteria.index', 'uses' => 'Admin\ProteriaController@index']);
	get('proteria/export', ['as' => 'admin.proteria.export', 'uses' => 'Admin\ProteriaController@getExport']);

	# receivals
	get('receivals', ['as' => 'admin.receivals.index', 'uses' => 'Admin\ReceivalsController@index']);
	get('receivals/create', ['as' => 'admin.receivals.create', 'uses' => 'Admin\ReceivalsController@create']);
	get('receivals/{receival}/edit', ['as' => 'admin.receivals.edit', 'uses' => 'Admin\ReceivalsController@edit']);
	put('receivals/{receival}', ['as' => 'admin.receivals.update', 'uses' => 'Admin\ReceivalsController@update']);
	post('receivals', ['as' => 'admin.receivals.store', 'uses' => 'Admin\ReceivalsController@store']);
	delete('receivals/{receival}', ['as' => 'admin.receivals.delete', 'uses' => 'Admin\ReceivalsController@store']);

	# design
	get('design', ['as' => 'admin.slides', 'uses' => 'Admin\SlidesController@index']);
	get('design/general', ['as' => 'admin.design', 'uses' => 'Admin\SettingsController@getDesign']);
	put('design/general', ['as' => 'admin.design.save', 'uses' => 'Admin\SettingsController@putDesign']);

	# slides
	put('design/slides/order', ['as' => 'admin.slides.order', 'uses' => 'Admin\SlidesController@order']);
	get('design/slides', ['as' => 'admin.slides.index', 'uses' => 'Admin\SlidesController@index']);
	post('design/slides', ['as' => 'admin.slides.store', 'uses' => 'Admin\SlidesController@store']);
	get('design/slides/{image}', ['as' => 'admin.slides.edit', 'uses' => 'Admin\SlidesController@edit']);
	put('design/slides/{image}', ['as' => 'admin.slides.update', 'uses' => 'Admin\SlidesController@update']);
	delete('design/slides/{image}', ['as' => 'admin.sides.destroy', 'uses' => 'Admin\SlidesController@destroy']);

	# banners
	get('design/banners', ['as' => 'admin.banners.index', 'uses' => 'Admin\BannersController@index']);
	put('design/banners', ['as' => 'admin.banners.update', 'uses' => 'Admin\BannersController@update']);
});


# STORE & CART ROUTES
get('checkout', ['as' => 'store.checkout', 'uses' => 'StoreController@checkout']);
get('success', ['as' => 'store.success', 'uses' => 'StoreController@success']);
post('push', ['as' => 'store.checkout.push', 'uses' => 'StoreController@push']);
post('cart/setsubscribe/{subscribe}', ['as' => 'store.setsubscribe', 'uses' => 'StoreController@setSubscribe']);
get('{path}', ['as' => 'store', 'uses' => 'StoreController@index'])->where('path', '[a-z0-9/-]+');