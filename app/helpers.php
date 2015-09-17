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