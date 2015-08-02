<?php namespace Friluft\Http\Controllers;

use Auth;
use Former;
use Friluft\Http\Requests;
use Friluft\Order;
use Input;

class UserController extends Controller {

	public function index()
	{
        return view('front.user')->with('aside', true);
	}

    public function getOrders() {
        return view('front.user_orders')->with([
			'orders' => Auth::user()->orders()->whereNotNull('reservation')->get(),
			'aside' => true,
		]);
    }

    public function getOrder(Order $order) {
        return view('front.user_order')->with([
			'order' => $order,
			'aside' => true,
		]);
    }

	public function getSettings() {
		$user = Auth::user();
		Former::populate($user);
		return view('front.user_settings')->with([
			'user' => Auth::user(),
			'aside' => true,
		]);
	}

	public function putSettings(Requests\SaveUserSettingsRequest $request) {
		# get user
		$user = Auth::user();

		$user->email = Input::get('email');
		$user->password = \Hash::make(Input::get('password'));

		# save
		$user->save();

		# redirect
		return \Redirect::route('user')->with('success', trans('store.user.settings saved'));
	}

}
