<ul class="nav vertical fancy">
<?php

	echo Friluft\Utils\Navigation::make([
		'fa-dashboard:Dashboard' => 'admin/dashboard',

		'fa-dropbox:Products' => ['admin/products', [
			'Create' => 'admin/products/create'
		]],

		'fa-sitemap:Categories' => ['admin/categories', [
			'Create' => 'admin/categories/create'
		]],

		'fa-shopping-cart:Orders' => 'admin/orders',
	])->render();

?>
</ul>