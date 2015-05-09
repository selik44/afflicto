<?php namespace Friluft\Http\Controllers;

class HomeController extends Controller {

	public function index()
	{
		return $this->view('front.home')
			->with([
				'slider' => true,
				'breadcrumbs' => false,
			]);
	}

	public function terms() {
		return view('front.terms');
	}

}