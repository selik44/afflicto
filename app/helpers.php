<?php

function form($path) {
	$path = trim(str_replace('.', '/', $path), '/');

	$file = base_path() .'/resources/forms/' .$path .'.php';
	if (file_exists($file)) {
		return include($file);
	}
	trigger_error("Form '" .$path ."' not found!");
}