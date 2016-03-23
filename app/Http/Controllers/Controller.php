<?php namespace Friluft\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Friluft\Store;
use View;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	protected function view($view) {
		$view = explode('.', $view);
		$name = array_pop($view);
		$base = implode('.', $view);

		$storeName = Store::current()->name;

		# get overriden view?
		$file = $base .'.' .$storeName .'.' .$name;
		if (View::exists($file)) {
			return View::make($file);
		}

		# get base view
		return View::make($base .'.' .$name);
	}

}
