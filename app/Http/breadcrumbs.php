<?php

Breadcrumbs::register('home', function($bc) {
	$bc->push('Home', url('/'));
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

Breadcrumbs::register('store', function($bc) {
	$bc->parent('home');
	$bc->push('Store', url('/store'));
});

Breadcrumbs::register('category', function($bc, $cat) {
	$bc->parent('store');

	$parents = [$cat];

	$p = $cat->parent;
	$last = ($p) ? false : true;
	while($last == false) {
		$parents[] = $p;
		$p = $p->parent;
		if (!$p) $last = true;
	}

	$parents = array_reverse($parents);

	$slug = '/store';
	foreach($parents as $parent) {
		$slug .= '/' .$parent->slug;
		$bc->push($parent->name, url($slug));
	}
});

Breadcrumbs::register('product', function($bc, $product, $category) {
	$bc->parent('category', $category);

	$path = $slug = $category->getPath() .'/' .$product->slug;

	$bc->push(e($product->name), url($path));
});

Breadcrumbs::register('cart', function($bc) {
	$bc->parent('store');

	$bc->push('Cart', route('cart.index'));
});