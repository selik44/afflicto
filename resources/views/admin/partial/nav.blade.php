<ul id="navigation" class="nav vertical fancy">
<?php

	$nav = Friluft\Utils\Navigation::make([
		'fa-dashboard:Dashboard' => 'admin/dashboard',

		'fa-dropbox:Products' => ['admin/products', [
			'All' => 'admin/products',
			'Create' => 'admin/products/create'
		]],

		'fa-sitemap:Categories' => ['admin/categories', [
			'All' => 'admin/categories',
			'Tree View' => 'admin/categories/tree',
			'Create' => 'admin/categories/create',
		]],

		'fa-shopping-cart:Orders' => 'admin/orders',
	]);

	echo $nav->render();

?>
</ul>

@section('scripts')
	@parent
	
	<script>
		var nav = $("#navigation");

		nav.find('li.current > ul').show();

		nav.find('li a').click(function(e) {
			//is submenu?
			var ul = $(this).siblings('ul');
			if (ul.length > 0) {
				ul.slideToggle();
				e.preventDefault();
			}
		});
	</script>
@stop