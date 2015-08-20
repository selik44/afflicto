<?php

namespace Friluft\Utils;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Request;
use URL;

class Navigation {

	private $prefix = '';

	public $items = [
		'Home' => '/',
		'Stuff' => ['stuff', [
			'HTML' => 'stuff/html',
			'CSS' => 'stuff/css',
		]],
		'About' => 'about',
		'Contact' => 'contact',
	];

	public $hideMenus = false;

	/**
	 * Make a new navigation instance, with items.
	 */
	public static function make($items, $prefix = '') {
		return new static($items, $prefix);
	}

	public function __construct($items, $prefix = '') {
		$this->items = $items;
		$this->prefix = trim($prefix, '/');
	}

	/**
	 * Render a single item from the $items array.
	 */
	public function renderItem($label, $data) {
		if ($data == 'divider') return '<li class="divider"></li>';
		$exploded = explode(':', $label);
		$icon = null;
		if (count($exploded) > 1) {
			$icon = $exploded[0];
			$label = $exploded[1];
		}

		# unpack path and children, if any.
		if (is_array($data)) {
			list($path, $children) = $data;
		}else {
			$path = $data;
		}



		$config = 'access.' .str_replace('.', '_', trim($path, '.'));
		# do we have access to it?
		$perms = \Config::get($config);

		if (!permission($perms)) {
			return;
		}

		$route = \Route::getRoutes()->getByName($path);
		if ($route) $route = $route->getName();

		$active = '';
		if ($route == Request::route()->getName()) {
			$active = 'current';
		}

		/*
		if ($route && Request::is($route->getPath() .'*')) {
			$active = 'current';
		}else if ($path == '/' && Request::is('/')) {
			$active = 'current';
		}else if(Request::route()->getName() == $path) {
			$active = 'current';
		}else {
			$active = '';
		}*/

		# build the string
		$str = '<li class="' .$active .'">';
			# get link
			if ($path == '#') {
				$link = $path;
			}else {
				if (!preg_match('/^http.+/', $path)) {
					$link = route($path);
				}else {
					$link = $path;
				}
			}

			# a tag
			$str .= '<a href="' .$link .'">';
				# icon?
				if ($icon != null) $str .= '<i class="fa ' .$icon .'"></i> ';

				# label
				$str .= '<span class="label">' .trans($label) .'</span>';
			$str .= '</a>';

			# render children, if any.
			if (isset($children)) {
				$str .= '<ul>';
				foreach($children as $label => $data) {
					$str .= $this->renderItem($label, $data);
				}
				$str .= '</ul>';
			}

		$str .= '</li>';

		return $str;
	}

	/**
	 * Render the entire navigation UL
	 */
	public function render() {
		$str = '';

		foreach($this->items as $label => $data) {
			$str .= $this->renderItem($label, $data);
		}

		return $str;
	}

}