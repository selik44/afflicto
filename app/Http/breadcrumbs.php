<?php

Breadcrumbs::register('home', function($bc) {
	$bc->push(trans('store.crumbs.home'), url('/'));
});

Breadcrumbs::register('page', function($bc, $page) {
	$bc->parent('home');
	$bc->push($page->title, url($page->slug));
});

Breadcrumbs::register('search', function($bc) {
	$bc->parent('home');
	$bc->push(trans('store.crumbs.search'), url('/search'));
});

Breadcrumbs::register('user', function($bc) {
    $bc->parent('home');
    $bc->push(trans('store.crumbs.user'), route('user'));
});

Breadcrumbs::register('user.order', function($bc) {
	$order = Request::route()->parameter('order');
    $bc->parent('user');
    $bc->push(trans('store.crumbs.user_order'), route('user.order', ['order' => $order]));
});

Breadcrumbs::register('user.settings', function($bc) {
	$bc->parent('user');
	$bc->push(trans('store.crumbs.user_settings'), route('user.settings'));
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

Breadcrumbs::register('store.checkout', function($bc) {
	$bc->parent('home');

	$bc->push(trans('store.crumbs.checkout'), route('store.checkout'));
});

Breadcrumbs::register('store.success', function($bc) {
	$bc->parent('home');
	$bc->push(trans('store.crumbs.success'), route('store.success'));
});