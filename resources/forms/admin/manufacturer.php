<?php

$form = new stdClass();

$form->open = Former::open(route('admin.manufacturers.store'))
	->method('POST')->class('vertical')
	->rules([
		'name' => 'required|max:255',
		'slug' => 'required|max:255',
	]);

$form->name = Former::text('name');
$form->slug = Former::text('slug');

return $form;