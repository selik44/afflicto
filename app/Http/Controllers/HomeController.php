<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Controllers\Admin\BannersController;

class HomeController extends Controller {

	public function index()
	{
		return $this->view('front.home')
			->with([
				'slider' => true,
				'breadcrumbs' => false,
				'images' => BannersController::getImages(),
			]);
	}

	public function terms() {
		return view('front.terms');
	}

}