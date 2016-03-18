<?php

function form($path, $data = []) {
	$path = trim(str_replace('.', '/', $path), '/');

	$__file = base_path() .'/resources/forms/' .$path .'.php';
	if (file_exists($__file)) {
		extract($data);
		return include($__file);
	}
	trigger_error("Form '" .$path ."' not found!");
}


function permission($perms) {
	if (func_num_args() > 1) $perms = func_get_args();

	$user = Auth::user();
	if (!$user) return false;

	$role = $user->role;
	if ($role) {
		if ($role->machine === 'superadmin') return true;
		return $role->has($perms);
	}

	return false;
}

function numberFormat($number) {
	return str_replace(',', '.', number_format($number));
}

/**
 * @param string|\Friluft\Page $page
 * @return string
 */
function page($page) {
	if (is_string($page)) {
		$page = \Friluft\Page::whereSlug($page)->first();
		if ( ! $page) return '';
	}

	return Pages::compile($page);
}