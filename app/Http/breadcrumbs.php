<?php

Breadcrumbs::register('home', function($bc) {
	$bc->push('Home', url('/'));
});

Breadcrumbs::register('terms-and-conditions', function($bc) {
	$bc->parent('home');
	$bc->push('Terms & Conditions', url('/terms-and-conditions'));
});

Breadcrumbs::register('about', function($bc) {
	$bc->parent('home');
	$bc->push('About', url('/about'));
});

Breadcrumbs::register('contact', function($bc) {
	$bc->parent('home');
	$bc->push('About', url('/contact'));
});

Breadcrumbs::register('faq', function($bc) {
	$bc->parent('home');
	$bc->push('FAQ', url('/faq'));
});

Breadcrumbs::register('search', function($bc) {
	$bc->parent('home');
	$bc->push('Search', url('/search'));
});

Breadcrumbs::register('user', function($bc) {
    $bc->parent('home');
    $bc->push('User', route('user'));
});

Breadcrumbs::register('user.orders', function($bc) {
    $bc->parent('user');
    $bc->push('Orders', route('user.orders.index'));
});

Breadcrumbs::register('user.order', function($bc, $order) {
    $bc->parent('user.orders');
    $bc->push('Order', route('user.order', ['order' => $order]));
});

Breadcrumbs::register('category', function($bc, $cat) {
	$bc->parent('home');

	$parents = [$cat];

	$p = $cat->parent;
	$last = ($p) ? false : true;
	while($last == false) {
		$parents[] = $p;
		$p = $p->parent;
		if (!$p) $last = true;
	}

	$parents = array_reverse($parents);

	$slug = '';
	foreach($parents as $parent) {
		$slug .= '/' .$parent->slug;
		$bc->push($parent->name, url($slug));
	}
});

Breadcrumbs::register('product', function($bc, $product, $category) {
	$bc->parent('category', $category);

	$path = $category->getPath() .'/' .$product->slug;

	$bc->push(e($product->name), url($path));
});

Breadcrumbs::register('store.cart', function($bc) {
	$bc->parent('home');
	$bc->push('Cart', route('store.cart'));
});

Breadcrumbs::register('store.checkout', function($bc) {
	$bc->parent('store.cart');

	$bc->push('Checkout', route('store.checkout'));
});

Breadcrumbs::register('store.success', function($bc) {
	$bc->parent('store.cart');
	$bc->push('Success', route('cart.success'));
});