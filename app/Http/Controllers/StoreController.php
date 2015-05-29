<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Requests;
use Friluft\Order;
use Friluft\Role;
use Friluft\User;
use Friluft\Category;
use Friluft\Product;
use Friluft\Store;
use Cart;
use Log;
use Input;
use Mail;

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

		$data = Cart::getKlarnaOrder(Input::get('klarna_order'));
		$data = $data->marshal();

		# create the order, unless it already exists.
		$order = Order::where('klarna_id', '=', Input::get('klarna_order'))->first();
		if (!$order) {
			$order = $this->createOrder(Input::get('klarna_order'));
		}

		return view('front.store.success');
	}

	public function push() {
		$id = Input::get('klarna_order');

		# get order model
		$order = Order::where('klarna_id', '=', $id)->first();
		if (!$order) {
			$order = $this->createOrder(Input::get('klarna_order'));
		}

		# get data
		$data = Cart::getKlarnaOrder($id)->marshal();
		$order->status = $data['ORDER_STATUS'];
		$order->save();

		# react to status change
		if ($order->status == 'checkout_complete') {
			# update the "sold" counter for the products
			foreach($data['items'] as $item) {
				$product = Product::find($item['reference']['id']);
				$product->sell($item['quantity']);
				$product->save();
			}
		}

		return response('OK', 200);
	}

	private function createOrder($id) {
		# get klarna order
		$data = Cart::getKlarnaOrder($id)
			->marshal();

		# decode the item reference JSON
		foreach($data['cart']['items'] as $key => $item) {
			$data['cart']['items'][$key]['reference'] = json_decode($item['reference'], true);
		}

		# create new order
		$order = new Order();

		$order->klarna_id = $id;
		$order->reservation = $data['reservation'];
		$order->items = $data['items'];
		$order->status = $data['ORDER_STATUS'];
		$order->total_price_excluding_tax = $data['total_price_excluding_tax'];
		$order->total_price_including_tax = $data['total_price_including_tax'];
		$order->total_tax_amount = $data['total_tax_amount'];
		$order->billing_address = $data['billing_address'];
		$order->shipping_address = $data['shipping_address'];
		$order->locale = $data['locale'];
		$order->purchase_country = $data['purchase_country'];
		$order->purchase_currency = $data['purchase_currency'];

		$email = $data['billing_address']['email'];
		$user = User::where('email', '=', $email)->first();

		# create a new user automatically?
		if (!$user) {
			$user = new User();
			$user->role()->associate(Role::where('machine', '=', 'regular')->first());
			$user->email = $email;

			# parse name
			$name = explode(' ', $data['billing_address']['given_name']);
			if (count($name) > 1) {
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
			}else {
				$user->firstname = implode(' ', $name);
			}

			# generate password
			$password = str_random(16);
			$user->password = bcrypt($password);

			# save
			$user->save();

			# welcome the user
			Mail::send('emails.store.welcome', ['title' => 'Your New Account', 'password' => $password], function($mail) use($user) {
				$mail->to('me@afflicto.net')->subject('Your new account at ' .Store::current()->name);
			});
		}

		$order->user()->associate($user);

		# save it
		$order->save();

		# notify user
		Mail::send('emails.store.order_received', ['title' => 'Order Received', 'order' => $order], function($mail) use($user) {
			$mail->to('me@afflicto.net')->subject('Your order at ' .Store::current()->name);
		});

		return $order;
	}

}
