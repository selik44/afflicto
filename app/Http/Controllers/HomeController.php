<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Controllers\Admin\BannersController;
use Friluft\Tag;
use Input;
use Mailchimp;

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

	public function nyhetsbrev_get() {
		return view('front.nyhetsbrev');
	}

	public function nyhetsbrev_post(Mailchimp $mailchimp) {
		try {
			$mailchimp
				->lists
				->subscribe(env('MAILCHIMP_NEWSLETTER_ID'), ['email' => Input::get('email')]);
		}catch(\Exception $e) {
			return \Redirect::back()->with('error', 'Noe gikk galt, oppga du en riktig epost-addresse?');
		}

		return \Redirect::home()->with('success', 'Din epost er registrert i nyhetsbrevet!');
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
			$mail->to('kundeservice@123friluft.no')->subject('Kontakt fra ' .$email);
		});

		return \Redirect::to('/')->with('success', 'Din melding er sendt!');
	}

	public function retur_post() {
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
			$mail->to('retur@123friluft.no')->subject($subject);
		});

		return \Redirect::to('/')->with('success', 'Din melding er sendt!');
	}

}