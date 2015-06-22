<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Requests;
use Input;
use Friluft\Product;

class SearchController extends Controller {

	public function index() {
		if (Input::has('terms')) {
			$terms = trim(substr(Input::get('terms'), 0, 60));
			return view('front.search')
				->with([
					'products' => Product::enabled()->search(Input::get('terms'))->get(),
					'aside' => true
				]);
		}
		
		return redirect('search')->with('error', 'A search term is required!');
	}

}
