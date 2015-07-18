<?php

$form = new stdClass();

$form->open = Former::open(route('admin.products.store'))
	->method('POST')
	->class('vertical')
	->rules([
		'name' => 'required|max:255',
		'slug' => 'required|max:255|slug|unique:products',
		'manufacturer' => 'required|integer',
		'categories' => 'array',
		'tags' => 'array',
		'summary' => 'max:600',
		'weight' => 'required|number|min:0',
		'price' => 'required|number|min:0',
		'inprice' => 'required|number|min:0',
		'vatgroup' => 'required|integer|exists:vatgroups',
		'stock' => 'required|integer|min:0',
		'enabled' => 'boolean',
		'barcode' => 'max:13|unique:products',
		'articlenumber' => 'required|max:255|unique:products',
	]);

$form->name = Former::text('name')->class('product-name');
$form->slug = Former::text('slug')->class('product-slug');

$form->articlenumber = Former::text('articlenumber');

$form->barcode = Former::text('barcode')->label(trans('admin.barcode'));

$form->manufacturer = Former::select('manufacturer_id')->label(trans('admin.manufacturer'))->fromQuery($manufacturers, 'name', 'id');
$form->summary = Former::textarea('summary');

$form->categories = Former::select('categories')->multiple()->fromQuery($categories, 'name', 'id')->name('categories[]')->label('categories');

$form->tags = Former::select('tags')->multiple()->fromQuery($tags, 'label', 'id')->name('tags[]')->label('tags');

$form->weight = Former::text('weight');
$form->price = Former::text('price');
$form->inprice = Former::text('inprice', 'In price');
$form->vatgroup = Former::select('vatgroup')->fromQuery($vatgroups, 'name', 'id');
$form->stock = Former::text('stock');
$form->enabled = Former::checkbox('enabled')->attr('value', 'on');

$form->description = Former::textarea('description')->rows(8);

return $form;