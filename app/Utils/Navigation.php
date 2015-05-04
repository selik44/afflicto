<?php

namespace Friluft\Utils;

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

		# active?
		if ($path == '/' && Request::is('/')) {
			$active = 'current';
		}else if (Request::is($path .'*')) {
			$active = 'current';
		}else {
			$active = '';
		}

		# build the string
		$str = '<li class="' .$active .'">';
			# a tag
			$str .= '<a href="' .URL::to($this->prefix .'/' .trim($path, '/')) .'">';
				# icon?
				if ($icon != null) $str .= '<i class="fa ' .$icon .'"></i> ';

				# label
				$str .= $label;
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