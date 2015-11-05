<?php

/**
 * This config file allows you to change the default behavior of Laratables, as well as register new TransformersProviders.
 * @package Gentlefox\Laratables
 */
return [
	
	# Out of the box, we configure Laratable to always use the 'escape' transformer on the data. (It runs it through the htmlentities function)
	'transformers' => '',

	/**
	 * Pagination
	 */
	'pagination' => [
		'enabled' => true,
		'perPage' => 15,
	],

	/**
	 * HTML
	 */
	'html' => [
		//the default css classes to apply to the html <table> element.
		'classes' => 'hovered bordered laratable',
	],


	'filters' => [
		'category' => '\Friluft\Laratables\CategoryFilter',
		'username' => '\Friluft\Laratables\UserNameFilter',
	],
];