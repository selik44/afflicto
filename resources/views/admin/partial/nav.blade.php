<ul id="navigation" class="nav vertical fancy">
<?php

	$nav = Friluft\Utils\Navigation::make([
		'fa-dashboard:admin.dashboard' => 'admin.dashboard',

        'fa-users:admin.users' => ['admin.users.index', [
             'fa-user:admin.all' => 'admin.users.index',
            'fa-plus:admin.new' => 'admin.users.create',
        ]],

        'fa-key:admin.roles' => ['admin.roles.index', [
            'fa-key:admin.all' => 'admin.roles.index',
            'fa-plus:admin.new' => 'admin.roles.create',
        ]],

        'fa-leaf:admin.manufacturers' => ['admin.manufacturers.index', [
            'fa-leaf:admin.all' => 'admin.manufacturers.index',
            'fa-plus:admin.add' => 'admin.manufacturers.create',
        ]],

		'fa-dropbox:admin.products' => ['admin.products.index', [
			'fa-leaf:admin.all' => 'admin.products.index',
			'fa-plus:admin.add' => 'admin.products.create',
		]],

		'fa-bars:admin.categories' => ['admin.categories.index', [
			'fa-bars:admin.all' => 'admin.categories.index',
			'fa-sitemap:admin.tree view' => 'admin.categories.tree',
			'fa-plus:admin.create' => 'admin.categories.create',
		]],

		'fa-tag:admin.tags' => ['admin.tags.index', [
			'fa-tag:admin.all' => 'admin.tags.index',
			'fa-plus:admin.add' => 'admin.tags.create',
		]],

		'fa-plus:admin.receivals' => ['admin.receivals.index', [
			'fa-list:admin.all' => 'admin.receivals.index',
			'fa-plus:admin.new' => 'admin.receivals.create',
		]],

		'fa-shopping-cart:admin.orders' => 'admin.orders.index',

        'fa-pencil:admin.design' => ['admin.slides.index', [
            'fa-newspaper:admin.slides' => 'admin.slides.index',
        ]],
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