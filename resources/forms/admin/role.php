<?php

$form = new stdClass();

$form->open = Former::open(route('admin.roles.store'))
	->method('POST')->class('vertical')
	->rules([
		'name' => 'required|max:255'
	]);

$form->name = Former::text('name');

return $form;