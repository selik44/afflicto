<ul class="nav clearfix">
<?php

	$nav = Friluft\Utils\Navigation::make([
		'fa-dashboard:admin.dashboard' => 'admin.dashboard',

        'fa-users:admin.users' => ['admin.users.index', [
            'fa-user:admin.customers' => 'admin.users.customers',
            'fa-user:admin.users' => 'admin.users.index',
            'fa-plus:admin.new user' => 'admin.users.create',
            'divider',
            'fa-key:admin.roles' => 'admin.roles.index',
            'fa-plus:admin.new role' => 'admin.roles.create',
        ]],

        'fa-file-text:admin.pages' => ['admin.pages.index', [
            'fa-file-text:admin.all' => 'admin.pages.index',
            'fa-plus:admin.new' => 'admin.pages.create',
        ]],

        'fa-dropbox:admin.products' => ['admin.products.index', [
            'fa-leaf:admin.products' => 'admin.products.index',
            'fa-edit:admin.quick edit' => 'admin.products.quick-edit',
            'fa-plus:admin.add product' => 'admin.products.create',
            'divider',
            'fa-adjust:admin.variants' => 'admin.variants.index',
            'fa-plus:admin.new variant' => 'admin.variants.create',
            'divider',
            'fa-tag:admin.tags' => 'admin.tags.index',
            'fa-plus:admin.new tag' => 'admin.tags.create',
			'divider',
			'fa-barcode:admin.coupons' => 'admin.coupons.index',
			'fa-plus:admin.new coupon' => 'admin.coupons.create',
        ]],

        'fa-bars:admin.categories' => ['admin.categories.index', [
            'fa-bars:admin.all' => 'admin.categories.index',
            'fa-sitemap:admin.tree view' => 'admin.categories.tree',
            'fa-plus:admin.create' => 'admin.categories.create',
        ]],

        'fa-leaf:admin.manufacturers' => ['admin.manufacturers.index', [
            'fa-plus:admin.manufacturers' => 'admin.manufacturers.index',
            'fa-plus:admin.add manufacturer' => 'admin.manufacturers.create',
        ]],

		'fa-shopping-cart:admin.orders' => ['admin.orders.index', [
            'fa-shopping-cart:admin.orders' => 'admin.orders.index',
            'fa-plus:admin.new order' => 'admin.orders.create',
            'fa-plane:admin.export to proteria' => 'admin.proteria.export',
            'divider',
            'fa-list:admin.receivals' => 'admin.receivals.index',
            'fa-plus:admin.new receival' => 'admin.receivals.create',
        ]],

        'fa-image:admin.design' => ['admin.slides', [
            'fa-pencil:admin.design' => 'admin.design',
            'fa-film:admin.slides' => 'admin.slides.index',
            'fa-image:admin.banners' => 'admin.banners.index',
        ]],

        'fa-gear:admin.settings' => 'admin.settings.index',
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