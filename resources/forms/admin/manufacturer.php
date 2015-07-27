<?php

$form = new stdClass();

$form->open = Former::open_for_files(route('admin.manufacturers.store'))
	->method('POST')->class('vertical')
	->rules([
		'name' => 'required|max:255',
		'slug' => 'required|max:255',
	]);

$form->name = Former::text('name');
$form->slug = Former::text('slug');
$form->description = Former::textarea('description');
$form->image = Former::file('logo');

return $form;