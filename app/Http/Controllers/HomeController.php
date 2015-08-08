<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Controllers\Admin\BannersController;
use Friluft\Tag;

class HomeController extends Controller {

	public function index()
	{
		$popular = Tag::whereType('popular')->first()->products()->orderByRaw('RAND()')->take(4)->get();
		$news = Tag::whereType('news')->first()->products()->orderByRaw('RAND()')->take(4)->get();

		$images = BannersController::getImages();

		return $this->view('front.home')
			->with([
				'slider' => true,
				'breadcrumbs' => false,
				'images' => $images,
				'popular' => $popular,
				'news' => $news,
			]);
	}

	public function terms() {
		return view('front.terms');
	}

}