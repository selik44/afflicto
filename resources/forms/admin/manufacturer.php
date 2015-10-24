<?php

$form = new stdClass();

$form->open = Former::open_for_files(route('admin.manufacturers.store'))
	->method('POST')->class('vertical')
	->rules([
		'name' => 'required|max:255',
		'slug' => 'required|max:255',
		'prepurchase_enabled' => 'boolean',
		'prepurchase_days' => 'integer|min:1',
	]);

$form->name = Former::text('name');
$form->slug = Former::text('slug');
$form->description = Former::textarea('description');
$form->image = Former::file('logo');
$form->prepurchase_enabled = Former::checkbox('prepurchase_enabled');
$form->prepurchase_days = Former::text('prepurchase_days');

return $form;