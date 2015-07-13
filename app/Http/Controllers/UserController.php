<?php namespace Friluft\Http\Controllers;

use Auth;
use Friluft\Http\Requests;
use Friluft\Order;

class UserController extends Controller {

	public function index()
	{
        return view('front.user');
	}

    public function getOrders() {
        return view('front.user_orders')->with('orders', Auth::user()->orders);
    }

    public function getOrder(Order $order) {
        return view('front.user_order')->with('order', $order);
    }

}
