<?php

namespace Friluft\Http\Controllers;

use Friluft\Http\Requests;
use Gentlefox\Mailchimp\Mailchimp;
use Response;
use Input;

class NewsletterController extends Controller
{

	public function register(Mailchimp $mailchimp) {
		if ( ! Input::has('email')) return Response::json(['status' => 'error', 'error' => 'Email is required.']);

		try {
			$mailchimp
				->lists()
				->subscribe(env('MAILCHIMP_NEWSLETTER_ID'), Input::get('email'));
		}catch(\Exception $e) {
			return Response::json(['status' => 'error', 'error' => 'Noe gikk galt. Feil e-mail addresse?'], 200);
		}

		return Response::json(['status' => 'success'], 200);
	}

	public function remove(Mailchimp $mailchimp) {
		if ( ! Input::has('email')) return Response::json(['status' => 'error', 'error' => 'Email er obligatorisk.']);

		try {
			$mailchimp
				->lists()
				->unsubscribe(env('MAILCHIMP_NEWSLETTER_ID'), Input::get('email'));
		}catch(\Exception $e) {
			return Response::json(['status' => 'error', 'error' => 'Noe gikk galt. Feil e-mail addresse?'], 200);
		}

		return Response::json(['status' => 'success'], 200);
	}

}
