<?php namespace Friluft\Http\Controllers;

use Auth;
use Friluft\Http\Requests;

class UserController extends Controller {

	public function index()
	{
        return view('front.user');
	}

    public function getOrders() {
        return view('front.user_orders')->with('orders', Auth::user()->orders);
    }

}
