<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Order;
use Friluft\User;
use Illuminate\Http\Request;
use Friluft\Category;
use Friluft\Product;
use Cart;
use Klarna_Checkout_Order;
use Klarna_Checkout_Connector;
use Log;
use Input;
use Session;

class StoreController extends Controller {

	public function index($path)
	{
		$path = explode('/', $path);
		$slug = array_pop($path);

		$cat = Category::where('slug', '=', $slug)->first();

		if ($cat) {
			return view('front.store_category')->with('category', $cat)->with('aside', true);
		}

		$product = Product::enabled()->where('slug', '=', $slug)->first();

		if ($product) {
			$slug = array_pop($path);
			
			if ($slug) {
				$category = Category::where('slug', '=', $slug)->first();
			}

			return view('front.store_product')
				->with([
					'category' => $category,
					'product' => $product,
					'aside' => true
				]);
		}

		abort(404);
	}

	/**
	 * Display the cart contents.
	 * @return View
	 */
	public function cart() {
		return view('front.store_cart')
			->with([
				'items' => Cart::getItemsWithModels(false),
				'total' => Cart::getTotal(),
			]);
	}

	/**
	 * Shows the checkout form
	 * @return View
	 */
	public function checkout() {
		# get the klarna order
		$order = Cart::getKlarnaOrder();

		return view('front.store.checkout')
		->with('snippet', $order['gui']['snippet']);
	}

	public function success() {
		if (!Input::has('klarna_order')) {
			return view('front.store.success')->with('error', 'Something went wrong, contact us if the issue persists.');
		}

		return view('front.store.success');
	}

	private function createOrder($data) {
		$order = new Order();

		# get the user
		$email = $data['billing_address']['email'];
		$user = User::where('email', '=', $email)->first();

		# create a new user automatically
		if (!$user) {
			$user = new User();
			$user->email = $email;

			# parse name
			$name = explode(' ', $data['billing_address']['given_name']);
			$firstname = '';
			$lastname = '';
			foreach($name as $key => $segment) {
				if ($key >= count($name)-1) {
					$lastname = $segment;
				}else {
					$firstname .= $segment .' ';
				}
			}
			$user->firstname = $firstname;
			$user->lastname = $lastname;

			# generate password
			$user->password = bcrypt(str_random(16));

			# save
			$user->save();
			Mail::send('store.welcome', ['password' => $user->password], function($mail) use($user) {
				$mail->to($user->email)->subject('Your new account at ' .\Store::current()->name);
			});
		}

		# associate the user and save
		$order->user()->associate($user);
		$order->save();

		# notify the user that we received the order
		Mail::send('store.order_received', ['items' => $data['cart']['items']], function($mail) use($user) {
			$mail->to($user->email)->subject('Your order at ' .\Store::current()->name);
		});

		return $order;
	}

	public function push() {
		# get the klarna order
		$id = Input::get('klarna_order');
		Log::info('Klarna pushed us with id: ' .$id, (array) Cart::getKlarnaOrder($id));

		# get order data
		$data = Cart::getKlarnaOrder($id);
		if (!$data) {
			Log::error('Klarna Push failed. Cannot find klarna order with ID of: ' .$id);
			return response('ERROR', 400);
		}

		# get data as array
		$data = $data->marshal();

		# parse data reference stuff
		foreach($data['cart']['items'] as $key => $item) {
			$data['cart']['items'][$key]['reference'] = json_decode($item['reference'], true);
		}

		# get order model
		$order = Order::where('klarna_id', '=', $id)->first();

		# if, for some reason, the user was never sent to /store/success,
		# we create the order now.
		if (!$order) {
			$order = $this->createOrder($data);
		}

		# update and save
		$order->data = $data;
		$order->save();

		# react to status change
		if ($data['ORDER_STATUS'] == 'checkout_complete') {

			# update the "sold" counter for the products
			foreach($data['items'] as $item) {
				$product = Product::find($item['reference']['id']);
				$product->sell($item['quantity']);
				$product->save();
			}
		}

		return response('OK', 200);
	}

}
