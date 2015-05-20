<?php

$form = new stdClass();

$form->open = Former::open(route('admin.users.store'))
	->method('POST')->class('vertical')
	->rules([
		'firstname' => 'required|max:60',
		'lastname' => 'required|max:255',
		'email' => 'required|email',
		'role_id' => 'required|number',
	]);

$form->firstname = Former::text('firstname');
$form->lastname = Former::text('lastname');
$form->email = Former::email('email');

$form->role_id = Former::select('role_id', 'Role')->fromQuery(\Friluft\Role::all(), 'name', 'id');

return $form;