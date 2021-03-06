<?php

$form = new stdClass();

$form->open = Former::open(route('admin.products.store'))
	->method('POST')
	->class('vertical')
	->rules([
		'name' => 'required|max:255',
		'slug' => 'required|max:255|slug|unique:products',
		'manufacturer_id' => 'required|integer',
		'sizemap_id' => 'integer',
		'categories' => 'array',
		'variants' => 'array',
		'tags' => 'array',
		'summary' => 'max:600',
		'weight' => 'required|number|min:0',
		'price' => 'required|number|min:0',
		'inprice' => 'required|number|min:0',
		'vatgroup' => 'required|integer|exists:vatgroups',
		'stock' => 'required|integer|min:0',
		'enabled' => 'boolean',
		'barcode' => 'max:13|unique:products',
		'articlenumber' => 'max:255|unique:products',
		'meta_description' => 'max:160',
		'meta_keywords' => 'max:250',
		'children' => 'array',
	]);

$form->name = Former::text('name')->class('product-name');
$form->slug = Former::text('slug')->class('product-slug');

$form->articlenumber = Former::text('articlenumber');

$form->barcode = Former::text('barcode')->label(trans('admin.barcode'));

$form->manufacturer = Former::select('manufacturer_id')->label(trans('admin.manufacturer'))->fromQuery($manufacturers, 'name', 'id');
$form->sizemap = Former::select('sizemap_id')->label('Størrelse-kart')->fromQuery($sizemaps, 'name', 'id');
$form->summary = Former::textarea('summary');

$form->categories = Former::select('categories')->multiple()->fromQuery($categories, 'name', 'id')->name('categories[]')->label('categories');

$form->variants = Former::select('variants')->multiple()->fromQuery($variants, 'admin_name', 'id')->name('variants[]')->label('variants');

$form->tags = Former::select('tags')->multiple()->fromQuery($tags, 'label', 'id')->name('tags[]')->label('tags');

$form->weight = Former::text('weight');
$form->price = Former::text('price');
$form->inprice = Former::text('inprice', 'In price');
$form->vatgroup = Former::select('vatgroup')->fromQuery($vatgroups, 'name', 'id');
$form->stock = Former::text('stock');
$form->enabled = Former::checkbox('enabled')->attr('value', 'on');

$form->description = Former::textarea('description')->rows(8);

$form->meta_description = Former::textarea('meta_description')->rows(4);
$form->meta_keywords = Former::textarea('meta_keywords')->rows(4);

$form->children = Former::select('children')->multiple()->fromQuery($products, 'name', 'id')->name('children[]')->label('products');

$form->order = Former::text('order');

return $form;