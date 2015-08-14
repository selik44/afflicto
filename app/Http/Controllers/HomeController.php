<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Controllers\Admin\BannersController;
use Friluft\Tag;
use Input;

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

	public function contact_post() {
		$validator = \Validator::make(Input::all(), [
			'name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'message' => 'required',
		]);

		if ($validator->fails()) {
			return \Redirect::back()->withErrors($validator);
		}

		$email = htmlentities(Input::get('email'));
		\Mail::send('emails.store.kontakt', ['input' => Input::all()], function($mail) use($email) {
			$mail->to('me@afflicto.net')->subject('Kontakt fra ' .$email);
		});

		return \Redirect::to('/')->with('success', 'Din melding er sendt!');
	}

	public function retur_post() {
		dd(Input::all());
		$validator = \Validator::make(Input::all(), [
			'name' => 'required',
			'order_id' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'varer' => 'required',
		]);

		if ($validator->fails()) {
			return \Redirect::back()->withErrors($validator);
		}

		$subject = 'Return';
		if (Input::has('order_id')) {
			$subject .=' #' .Input::get('order_id');
		}

		\Mail::send('emails.store.retur', ['input' => Input::all()], function($mail) use($subject) {
			$mail->to('me@afflicto.net')->subject($subject);
		});

		return \Redirect::to('/')->with('success', 'Din melding er sendt!');
	}

}