<?php

return [

	'admin' => 'admin.access',
	'admin_dashboard' => 'admin.access,admin.dashboard.view',

	'admin_products_index' => 'admin.access,products.view',
	'admin_products_create' => 'admin.access,products.create',
	'admin_products_store' => 'admin.access,products.create',
	'admin_products_show' => 'admin.access,products.view',
	'admin_products_edit' => 'admin.access,products.edit',
	'admin_products_update' => 'admin.access,products.edit',
	'admin_products_destroy' => 'admin.access,products.delete',

	'admin_categories_tree' => 'admin.access,categories.view',
	'admin_categories_tree_update' => 'admin.access,categories.edit',

	'admin_categories_index' => 'admin.access,categories.view',
	'admin_categories_create' => 'admin.access,categories.create',
	'admin_categories_store' => 'admin.access,categories.create',
	'admin_categories_edit' => 'admin.access,categories.edit',
	'admin_categories_update' => 'admin.access,categories.edit',
	'admin_categories_destroy' => 'admin.access,categories.delete',

	'admin_manufacturers_index' => 'admin.access,manufacturers.view',
	'admin_manufacturers_show' => 'admin.access,manufacturers.view',
	'admin_manufacturers_create' => 'admin.access,manufacturers.create',
	'admin_manufacturers_store' => 'admin.access,manufacturers.create',
	'admin_manufacturers_edit' => 'admin.access,manufacturers.edit',
	'admin_manufacturers_update' => 'admin.access,manufacturers.edit',

	'admin_users_index' => 'admin.access,users.view',
	'admin_users_show' => 'admin.access,users.view',
	'admin_users_create' => 'admin.access,users.create',
	'admin_users_store' => 'admin.access,users.create',
	'admin_users_edit' => 'admin.access,users.edit',
	'admin_users_update' => 'admin.access,users.edit',
];