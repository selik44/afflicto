<?php

# API ROUTES
get('api/cart', ['as' => 'api.cart.index', 'uses' => 'CartController@index']);
get('api/cart/clear', ['as' => 'api.cart.clear', 'uses' => 'CartController@clear']);
get('api/cart/saved', ['as' => 'api.cart.saved', 'uses' => 'CartController@getSaved']);
get('api/cart/{cart}', ['as' => 'api.cart.show', 'uses' => 'CartController@show']);
post('api/cart', ['as' => 'api.cart.store', 'uses' => 'CartController@store']);
put('api/cart/{id}/quantity', ['as' => 'api.cart.quantity', 'uses' => 'CartController@setQuantity']);
delete('api/cart/{id}', ['as' => 'api.cart.destroy', 'uses' => 'CartController@destroy']);
put('api/cart/coupons/{code}', ['as' => 'api.cart.coupons.store', 'uses' => 'CartController@addCouponCode']);

# newsletter API
post('api/newsletter', ['as' => 'api.newsletter.register', 'uses' => 'NewsletterController@register']);
delete('api/newsletter', ['as' => 'api.newsletter.remove', 'uses' => 'NewsletterController@remove']);

get('api/proteria/update', ['as' => 'api.proteria.update', 'uses' => 'Admin\ProteriaController@update']);
get('api/proteria/orders', ['as' => 'admin.proteria.export', 'uses' => 'Admin\ProteriaController@getExport']);

# ADMIN ROUTES
Route::group(['middleware' => 'perms:admin.access', 'prefix' => 'admin'], function() {


	# API
	Route::group(['prefix' => 'api'], function() {
		# enable/disable product
		put('products/{product}/setenabled', ['as' => 'admin.api.products.setEnabled', 'uses' => 'Admin\APIController@products_setEnabled', 'middleware' => 'perms:products.edit']);

		# get categories for product
		get('products/{product}/categories', ['as' => 'admin.api.products.getCategories', 'uses' => 'Admin\APIController@products_getCategories', 'middleware' => 'perms:products.view']);

		# sync categories
		post('products/{product}/categories', ['as' => 'admin.api.products.syncCategories', 'uses' => 'Admin\APIController@products_syncCategories', 'middleware' => 'perms:products.edit']);

		# add product image
		post('products/{product}/images', ['as' => 'admin.api.products.postImage', 'uses' => 'Admin\APIController@products_postImage', 'middleware' => 'perms:products.edit']);

		# update image order
		put('products/{product}/images/order', ['as' => 'admin.api.products.setImageOrder', 'uses' => 'Admin\APIController@products_setImageOrder', 'middleware' => 'perms:products.edit']);

		# delete product image
		delete('products/{product}/images', ['as' => 'admin.api.products.destroyImage', 'uses' => 'Admin\APIController@products_destroyImage', 'middleware' => 'perms:products.edit']);

		# add variant
		post('products/{product}/variants', ['as' => 'admin.api.products.addVariant', 'uses' => 'Admin\APIController@products_addVariant', 'middleware' => 'perms:products.edit']);

		# update variant
		put('products/{product}/variants/{variant}', ['as' => 'admin.api.products.updateVariant', 'uses' => 'Admin\APIController@products_updateVariant', 'middleware' => 'perms:products.edit']);

		# update variant stock
		put('products/{product}/variants/{variant}/setstock', ['as' => 'admin.api.products.setVariantsStock', 'uses' => 'Admin\APIController@products_setVariantsStock', 'middleware' => 'perms:products.edit']);

		# remove variant
		delete('products/{product}/variants/{variant}', ['as' => 'admin.api.products.removeVariant', 'uses' => 'Admin\APIController@products_removeVariant', 'middleware' => 'perms:products.edit']);

		# remove tab
		delete('products/tabs/{producttab}', ['as' => 'admin.api.products.removeTab', 'uses' => 'Admin\APIController@products_destroyTab', 'middleware' => 'perms:products.edit']);
	});

	# dashboard
	get('/', ['as' => 'admin', 'uses' => 'Admin\DashboardController@index']);
	get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@index', 'middleware' => 'perms:admin.dashboard.view']);


    #reviews
    get('users/reviews', ['as' => 'admin.users.reviews', 'uses' => 'Admin\UsersController@review']);
    post('users/reviews/{review}', ['as' => 'admin.review.approve', 'uses' => 'Admin\UsersController@approveReview']);
    delete('users/reviews/{review}', ['as' => 'admin.review.delete', 'uses' => 'Admin\UsersController@destroyReview', 'middleware' => 'perms:review.delete']);
	# users
	get('users', ['as' => 'admin.users.index', 'uses' => 'Admin\UsersController@index', 'middleware' => 'perms:users.view']);
	get('users/customers', ['as' => 'admin.users.customers', 'uses' => 'Admin\UsersController@customers', 'middleware' => 'perms:users.view']);
	get('users/create', ['as' => 'admin.users.create', 'uses' => 'Admin\UsersController@create', 'middleware' => 'perms:users.create']);
	post('users', ['as' => 'admin.users.store', 'uses' => 'Admin\UsersController@store', 'middleware' => 'perms:users.create']);
	get('users/{user}/edit', ['as' => 'admin.users.edit', 'uses' => 'Admin\UsersController@edit', 'middleware' => 'perms:users.edit']);
	get('users/{user}', ['as' => 'admin.users.show', 'uses' => 'Admin\UsersController@show', 'middleware' => 'perms:users.view']);
	put('users/{user}', ['as' => 'admin.users.update', 'uses' => 'Admin\UsersController@update', 'middleware' => 'perms:users.edit']);
	delete('users/{user}', ['as' => 'admin.users.delete', 'uses' => 'Admin\UsersController@destroy', 'middleware' => 'perms:users.delete']);


	# roles
	get('roles', ['as' => 'admin.roles.index', 'uses' => 'Admin\RolesController@index', 'middleware' => 'perms:roles.view']);
	post('roles', ['as' => 'admin.roles.store', 'uses' => 'Admin\RolesController@store', 'middleware' => 'perms:roles.create']);
	get('roles/create', ['as' => 'admin.roles.create', 'uses' => 'Admin\RolesController@create', 'middleware' => 'perms:roles.create']);
	get('roles/{role}/edit', ['as' => 'admin.roles.edit', 'uses' => 'Admin\RolesController@edit', 'middleware' => 'perms:roles.edit']);
	get('roles/{role}', ['as' => 'admin.roles.show', 'uses' => 'Admin\RolesController@show', 'middleware' => 'perms:roles.view']);
	put('roles/{role}', ['as' => 'admin.roles.update', 'uses' => 'Admin\RolesController@update', 'middleware' => 'perms:roles.edit']);
	delete('roles/{role}', ['as' => 'admin.roles.delete', 'uses' => 'Admin\RolesController@destroy', 'middleware' => 'perms:roles.delete']);

	# products
	delete('products/batch', ['as' => 'admin.products.batch.destroy', 'uses' => 'Admin\ProductsController@batchDestroy', 'middleware' => 'perms:products.delete']);
	put('products/batch/move', ['as' => 'admin.products.batch.move', 'uses' => 'Admin\ProductsController@batchMove', 'middleware' => 'perms:products.edit']);

	get('products/quick-edit', ['as' => 'admin.products.quick-edit', 'uses' => 'Admin\ProductsController@getMultiedit', 'middleware' => 'perms:products.edit']);
	put('products/quick-edit', ['as' => 'admin.products.quick-edit.save', 'uses' => 'Admin\ProductsController@putMultiedit', 'middleware' => 'perms:products.edit']);

	get('products', ['as' => 'admin.products.index', 'uses' => 'Admin\ProductsController@index', 'middleware' => 'perms:products.view']);
	get('products/create', ['as' => 'admin.products.create', 'uses' => 'Admin\ProductsController@create', 'middleware' => 'perms:products.create']);
	get('products/{product}/edit', ['as' => 'admin.products.edit', 'uses' => 'Admin\ProductsController@edit', 'middleware' => 'perms:products.edit']);
	#get('products/{product}', ['as' => 'admin.products.show', 'uses' => 'Admin\ProductsController@show', 'middleware' => 'perms:products.view']);
	put('products/{product}', ['as' => 'admin.products.update', 'uses' => 'Admin\ProductsController@update', 'middleware' => 'perms:products.edit']);
	put('products/{product}/relate/{related}', ['as' => 'admin.products.relate', 'uses' => 'Admin\ProductsController@relate', 'middleware' => 'perms:products.edit']);
	put('products/{product}/unrelate/{related}', ['as' => 'admin.products.unrelate', 'uses' => 'Admin\ProductsController@unrelate', 'middleware' => 'perms:products.edit']);
	post('products', ['as' => 'admin.products.store', 'uses' => 'Admin\ProductsController@store','middleware' => 'perms:products.create']);
	delete('products/{product}', ['as' => 'admin.products.delete', 'uses' => 'Admin\ProductsController@destroy', 'middleware' => 'perms:products.delete']);

	# clone
	get('products/clone/{product}', ['as' => 'admin.products.clone', 'uses' => 'Admin\ProductsController@cloneProduct', 'middleware' => 'perms:products.create']);

	# variants
	get('variants', ['as' => 'admin.variants.index', 'uses' => 'Admin\VariantsController@index', 'middleware' => 'perms:variants.view']);
	get('variants/create', ['as' => 'admin.variants.create', 'uses' => 'Admin\VariantsController@create', 'middleware' => 'perms:variants.create']);
	get('variants/{variant}/edit', ['as' => 'admin.variants.edit', 'uses' => 'Admin\VariantsController@edit', 'middleware' => 'perms:variants.edit']);
	put('variants/{variant}', ['as' => 'admin.variants.update', 'uses' => 'Admin\VariantsController@update', 'middleware' => 'perms:variants.edit']);
	post('variants', ['as' => 'admin.variants.store', 'uses' => 'Admin\VariantsController@store', 'middleware' => 'perms:variants.create']);
	delete('variants/{variant}', ['as' => 'admin.variants.destroy', 'uses' => 'Admin\VariantsController@destroy', 'middleware' => 'perms:variants.delete']);

	# categories
	get('categories/tree', ['as' => 'admin.categories.tree', 'uses' => 'Admin\CategoriesController@tree', 'middleware' => 'perms:categories.view']);
	put('categories/tree', ['as' => 'admin.categories.tree.update', 'uses' => 'Admin\CategoriesController@tree_update', 'middleware' => 'perms:categories.edit']);

	get('categories/tree/{category}', ['as' => 'html.category.products', 'uses' => 'Admin\CategoriesController@tree_getProducts', 'middleware' => 'perms:categories.view']);

	get('categories', ['as' => 'admin.categories.index', 'uses' => 'Admin\CategoriesController@index', 'middleware' => 'perms:categories.view']);
	get('categories/create', ['as' => 'admin.categories.create', 'uses' => 'Admin\CategoriesController@create', 'middleware' => 'perms:categories.create']);
	get('categories/{category}/edit', ['as' => 'admin.categories.edit', 'uses' => 'Admin\CategoriesController@edit', 'middleware' => 'perms:categories.edit']);
	get('categories/{category}', ['as' => 'admin.categories.show', 'uses' => 'Admin\CategoriesController@show', 'middleware' => 'perms:categories.view']);
	put('categories/{category}', ['as' => 'admin.categories.update', 'uses' => 'Admin\CategoriesController@update', 'middleware' => 'perms:categories.edit']);
	post('categories', ['as' => 'admin.categories.store', 'uses' => 'Admin\CategoriesController@store', 'middleware' => 'perms:categories.create']);
	delete('categories/{category}', ['as' => 'admin.categories.destroy', 'uses' => 'Admin\CategoriesController@destroy', 'middleware' => 'perms:categories.delete']);

	# tags
	get('tags', ['as' => 'admin.tags.index', 'uses' => 'Admin\TagsController@index', 'middleware' => 'perms:tags.view']);
	get('tags/create', ['as' => 'admin.tags.create', 'uses' => 'Admin\TagsController@create', 'middleware' => 'perms:tags.create']);
	get('tags/{tag}/edit', ['as' => 'admin.tags.edit', 'uses' => 'Admin\TagsController@edit', 'middleware' => 'perms:tags.edit']);
	put('tags/{tag}', ['as' => 'admin.tags.update', 'uses' => 'Admin\TagsController@update', 'middleware' => 'perms:tags.edit']);
	post('tags', ['as' => 'admin.tags.store', 'uses' => 'Admin\TagsController@store', 'middleware' => 'perms:tags.create']);
	delete('tags/{tag}', ['as' => 'admin.tags.destroy', 'uses' => 'Admin\TagsController@destroy', 'middleware' => 'perms:delete']);

	# manufacturers
	get('manufacturers', ['as' => 'admin.manufacturers.index', 'uses' => 'Admin\ManufacturersController@index', 'middleware' => 'perms:manufacturers.view']);
	get('manufacturers/create', ['as' => 'admin.manufacturers.create', 'uses' => 'Admin\ManufacturersController@create', 'middleware' => 'perms:manufacturers.create']);
	get('manufacturers/{manufacturer}/edit', ['as' => 'admin.manufacturers.edit', 'uses' => 'Admin\ManufacturersController@edit', 'middleware' => 'perms:manufacturers.edit']);
	get('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.show', 'uses' => 'Admin\ManufacturersController@show', 'middleware' => 'perms:manufacturers.view']);
	put('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.update', 'uses' => 'Admin\ManufacturersController@update', 'middleware' => 'perms:manufacturers.edit']);
	post('manufacturers', ['as' => 'admin.manufacturers.store', 'uses' => 'Admin\ManufacturersController@store', 'middleware' => 'perms:manufacturers.create']);
	delete('manufacturers/{manufacturer}', ['as' => 'admin.manufacturers.delete', 'uses' => 'Admin\ManufacturersController@destroy', 'middleware' => 'perms:manufacturers.delete']);

	# sizemaps
	get('sizemaps', ['as' => 'admin.sizemaps.index', 'uses' => 'Admin\SizemapsController@index', 'middleware' => 'perms:sizemaps.view']);
	get('sizemaps/create', ['as' => 'admin.sizemaps.create', 'uses' => 'Admin\SizemapsController@create', 'middleware' => 'perms:sizemaps.create']);
	post('sizemaps', ['as' => 'admin.sizemaps.store', 'uses' => 'Admin\SizemapsController@store', 'middleware' => 'perms:sizemaps.create']);
	get('sizemaps/{sizemap}/edit', ['as' => 'admin.sizemaps.edit', 'uses' => 'Admin\SizemapsController@edit', 'middleware' => 'perms:sizemaps.edit']);
	put('sizemaps/{sizemap}', ['as' => 'admin.sizemaps.update', 'uses' => 'Admin\SizemapsController@update', 'middleware' => 'perms:sizemaps.edit']);
	delete('sizemaps/{sizemap}', ['as' => 'admin.sizemaps.delete', 'uses' => 'Admin\SizemapsController@destroy', 'middleware' => 'perms:sizemaps.delete']);

	# orders
	get('orders', ['as' => 'admin.orders.index', 'uses' => 'Admin\OrdersController@index', 'middleware' => 'perms:orders.view']);
	get('orders/create', ['as' => 'admin.orders.create', 'uses' => 'Admin\OrdersController@create', 'middleware' => 'perms:orders.create']);
	get('orders/{order}/edit', ['as' => 'admin.orders.edit', 'uses' => 'Admin\OrdersController@edit', 'middleware' => 'perms:orders.edit']);
	put('orders/{order}', ['as' => 'admin.orders.update', 'uses' => 'Admin\OrdersController@update', 'middleware' => 'perms:orders.edit']);
	post('orders', ['as' => 'admin.orders.store', 'uses' => 'Admin\OrdersController@store', 'middleware' => 'perms:orders.create']);
	delete('orders/{order}', ['as' => 'admin.orders.delete', 'uses' => 'Admin\OrdersController@destroy', 'middleware' => 'perms:orders.delete']);

	get('orders/{order}/edit/products', ['as' => 'admin.orders.edit.products', 'uses' => 'Admin\OrdersController@products_edit', 'middleware' => 'perms:orders.edit']);
	put('orders/{order}/edit/products', ['as' => 'admin.orders.edit.products.update', 'uses' => 'Admin\OrdersController@products_update', 'middleware' => 'perms:orders.edit']);

	get('orders/{order}/packlist', ['as' => 'admin.orders.packlist', 'uses' => 'Admin\OrdersController@packlist', 'middleware' => 'perms:orders.view']);
	get('orders/packlist/{orders}', ['as' => 'admin.orders.multipacklist', 'uses' => 'Admin\OrdersController@getMultiPacklist', 'middleware' => 'perms:orders.view'])
		->where('orders', '.+');
	get('orders/status/{orders}/{status}', ['as' => 'admin.orders.status', 'uses' => 'Admin\OrdersController@update_status', 'middleware' => 'perms:orders.edit']);

	# proteria
	get('proteria', ['as' => 'admin.proteria.index', 'uses' => 'Admin\ProteriaController@index']);
	get('proteria/export', ['as' => 'admin.proteria.export', 'uses' => 'Admin\ProteriaController@getExport']);

	# Receivals
	get('receivals', ['as' => 'admin.receivals.index', 'uses' => 'Admin\ReceivalsController@index', 'middleware' => 'perms:receivals.view']);

	# create & store
	get('receivals/create', ['as' => 'admin.receivals.create', 'uses' => 'Admin\ReceivalsController@create', 'middleware' => 'perms:receivals.create']);
	post('receivals', ['as' => 'admin.receivals.store', 'uses' => 'Admin\ReceivalsController@store', 'middleware' => 'perms:receivals.create']);

	# edit & update
	get('receivals/{receival}/edit', ['as' => 'admin.receivals.edit', 'uses' => 'Admin\ReceivalsController@edit', 'middleware' => 'perms:receivals.edit']);
	get('receivals/line/{receival}/{product}', ['as' => 'admin.receivals.getLine', 'uses' => 'Admin\ReceivalsController@getLine', 'middleware' => 'perms:receivals.edit']);
	put('receivals/{receival}', ['as' => 'admin.receivals.update', 'uses' => 'Admin\ReceivalsController@update', 'middleware' => 'perms:receivals.edit']);

	# get packlist
	get('receivals/{receival}/packlist', ['as' => 'admin.receivals.packlist', 'uses' => 'Admin\ReceivalsController@getPacklist', 'middleware' => 'perms:receivals.edit']);

	# get the variant-info for a product
	get('receivals/variants/{product}', ['as' => 'admin.receivals.variants', 'uses' => 'Admin\ReceivalsController@getVariants', 'middleware' => 'perms:receivals.edit']);

	# get receival page
	get('receivals/{receival}/receive', ['as' => 'admin.receivals.receive', 'uses' => 'Admin\ReceivalsController@getReceive', 'middleware' => 'perms:receivals.edit']);

	# apply receival
	put('receivals/{receival}/receive', ['as' => 'admin.receivals.receive.store', 'uses' => 'Admin\ReceivalsController@putReceive', 'middleware' => 'perms:receivals.edit']);

	# delete
	delete('receivals/{receival}', ['as' => 'admin.receivals.destroy', 'uses' => 'Admin\ReceivalsController@destroy', 'middleware' => 'perms:receivals.delete']);

	# pages
	get('pages', ['as' => 'admin.pages.index', 'uses' => 'Admin\PagesController@index', 'middleware' => 'perms:pages.view']);
	get('pages/create', ['as' => 'admin.pages.create', 'uses' => 'Admin\PagesController@create', 'middleware' => 'perms:pages.create']);
	post('pages', ['as' => 'admin.pages.store', 'uses' => 'Admin\PagesController@store', 'middleware' => 'perms:pages.create']);
	get('pages/{page}/edit', ['as' => 'admin.pages.edit', 'uses' => 'Admin\PagesController@edit', 'middleware' => 'perms:pages.edit']);
	put('pages/{page}', ['as' => 'admin.pages.update', 'uses' => 'Admin\PagesController@update', 'middleware' => 'perms:pages.edit']);
	delete('pages/{page}', ['as' => 'admin.pages.destroy', 'uses' => 'Admin\PagesController@destroy', 'middleware' => 'perms:pages.delete']);

	# settings
	get('settings', ['as' => 'admin.settings.index', 'uses' => 'Admin\SettingsController@index', 'middleware' => 'perms:settings.edit']);
	put('settings', ['as' => 'admin.settings.update', 'uses' => 'Admin\SettingsController@update', 'middleware' => 'perms:settings.edit']);

	# design
	get('design', ['as' => 'admin.slides', 'uses' => 'Admin\SlidesController@index', 'middleware' => 'perms:design.edit']);
	get('design/general', ['as' => 'admin.design', 'uses' => 'Admin\DesignController@getDesign', 'middleware' => 'perms:design.edit']);
	put('design/general', ['as' => 'admin.design.save', 'uses' => 'Admin\DesignController@putDesign', 'middleware' => 'perms:design.edit']);

	# slides
	put('design/slides/order', ['as' => 'admin.slides.order', 'uses' => 'Admin\SlidesController@order', 'middleware' => 'perms:design.edit']);
	get('design/slides', ['as' => 'admin.slides.index', 'uses' => 'Admin\SlidesController@index', 'middleware' => 'perms:design.edit']);
	post('design/slides', ['as' => 'admin.slides.store', 'uses' => 'Admin\SlidesController@store', 'middleware' => 'perms:design.edit']);
	get('design/slides/{image}', ['as' => 'admin.slides.edit', 'uses' => 'Admin\SlidesController@edit', 'middleware' => 'perms:design.edit']);
	put('design/slides/{image}', ['as' => 'admin.slides.update', 'uses' => 'Admin\SlidesController@update', 'middleware' => 'perms:design.edit']);
	delete('design/slides/{image}', ['as' => 'admin.sides.destroy', 'uses' => 'Admin\SlidesController@destroy', 'middleware' => 'perms:design.edit']);

	# banners
	get('design/banners', ['as' => 'admin.banners.index', 'uses' => 'Admin\BannersController@index', 'middleware' => 'perms:design.edit']);
	put('design/banners', ['as' => 'admin.banners.update', 'uses' => 'Admin\BannersController@update', 'middleware' => 'perms:design.edit']);

	# tiles
	get('design/tiles', ['as' => 'admin.tiles.index', 'uses' => 'Admin\TilesController@index', 'middleware' => 'perms:tiles.edit']);
	put('design/tiles', ['as' => 'admin.tiles.update', 'uses' => 'Admin\TilesController@update', 'middleware' => 'perms:tiles.edit']);

	# coupons
	get('coupons', ['as' => 'admin.coupons.index', 'uses' => 'Admin\CouponsController@index', 'middleware' => 'perms:coupons.view']);
	get('coupons/create', ['as' => 'admin.coupons.create', 'uses' => 'Admin\CouponsController@create', 'middleware' => 'perms:coupons.create']);
	post('coupons', ['as' => 'admin.coupons.store', 'uses' => 'Admin\CouponsController@store', 'middleware' => 'perms:coupons.create']);
	get('coupons/{coupon}/edit', ['as' => 'admin.coupons.edit', 'uses' => 'Admin\CouponsController@edit', 'middleware' => 'perms:coupons.edit']);
	put('coupons/{coupon}/edit', ['as' => 'admin.coupons.update', 'uses' => 'Admin\CouponsController@update', 'middleware' => 'perms:coupons.update']);
	delete('coupons/{coupon}', ['as' => 'admin.coupons.destroy', 'uses' => 'Admin\CouponsController@destroy', 'middleware' => 'perms:coupons.delete']);

	# reports
	get('reports/profit', ['as' => 'admin.reports.profit', 'uses' => 'Admin\ReportsController@profit', 'middleware' => 'perms:reports.view']);
	get('reports/users', ['as' => 'admin.reports.users', 'uses' => 'Admin\ReportsController@users', 'middleware' => 'perms:reports.view']);
	get('reports/products', ['as' => 'admin.reports.products', 'uses' => 'Admin\ReportsController@products', 'middleware' => 'perms:reports.view']);

	get('reports/products/export', ['as' => 'admin.reports.products.excel', 'uses' => 'Admin\ReportsController@exportProducts', 'middleware' => 'perms:reports.view']);

	# exports
	get('export/products', ['as' => 'admin.export.products', 'uses' => 'Admin\ExportController@products']);

});

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

# newsletter routes
post('nyhetsbrev', ['as' => 'nyhetsbrev.post', 'uses' => 'HomeController@nyhetsbrev_post']);

# HOME, STORE & CART ROUTES
Route::group(['middleware' => 'popup'], function() {
	# HOME ROUTES
	get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
	get('search', ['as' => 'search', 'uses' => 'SearchController@index']);

	post('kontakt', ['as' => 'contact.post', 'uses' => 'HomeController@contact_post']);
	post('retur', ['as' => 'retur.post', 'uses' => 'HomeController@retur_post']);

	post('partners', ['as' => 'partners.post', 'uses' => 'HomeController@partners_post']);

	# store / cart routes
	get('checkout', ['as' => 'store.checkout', 'uses' => 'StoreController@checkout']);
	get('success', ['as' => 'store.success', 'uses' => 'StoreController@success']);
	post('push', ['as' => 'store.checkout.push', 'uses' => 'StoreController@push']);
	post('cart/setsubscribe/{subscribe}', ['as' => 'store.setsubscribe', 'uses' => 'StoreController@setSubscribe']);
	get('cart/clear', ['as' => 'store.clear', 'uses' => 'StoreController@clearCart']);
	get('{path}', ['as' => 'store', 'uses' => 'StoreController@index'])->where('path', '[a-z0-9/-]+');
    post('{path}', ['as' => 'store.reviews', 'uses' => 'StoreController@review'])->where('path', '[a-z0-9/-]+');
});