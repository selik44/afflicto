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
use Session;

class StoreController extends Controller {

	public function index($path)
	{
		$query = $path;
		$path = explode('/', $path);
		$slug = array_pop($path);

		foreach(Category::where('slug', '=', $slug)->get() as $cat) {
			$p = $cat->getPath();

			if ($p == $query) {
				$products = $cat->nestedProducts();

				$manufacturers = [];
				foreach($products as $product) {
					$m = $product->manufacturer;
					if ( ! $m) continue;
					if ( ! isset($manufacturers[$m->id])) {
						$manufacturers[$m->id] = $m;
					}
				}

				# create merged variant filters
				$variants = [];
				foreach($products as $product) {
					foreach($product->variants as $variant) {
						$name = strtolower(str_replace(' ', '-', $variant->name));
						if ( ! isset($variants[$name])) $variants[$name] = [];
						$variants[$name] = array_unique(array_merge($variants[$name], array_column($variant->data['values'], 'name')));
					}
				}

				return view('front.store_category')
					->with([
						'category' => $cat,
						'products' => $products,
						'manufacturers' => $manufacturers,
						'variants' => $variants,
						'aside' => true,
					]);
			}
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

	public function cart() {
		if (count(Cart::getItems()) <= 0) {
			return \Redirect::back()->with('info', 'Handlekurven er tom!');
		}
		return view('front.store.cart')
			->with([
				'items' => Cart::getItemsWithModels(false),
				'shipping' => Cart::getShipping(),
				'total' => Cart::getTotal(),
				'aside' => true,
				'intro' => true,
			]);
	}

	public function checkout() {
		if (Cart::nothing()) return \Redirect::home()->with('error', trans('store.your cart is empty'));

		# get the klarna order
		if (Session::has('klarna_order')) {
			$order = Cart::getKlarnaOrder(Session::get('klarna_order'));
		}else {
			$order = Cart::getKlarnaOrder();
		}

		return view('front.store.checkout')
			->with([
				'snippet' => $order['gui']['snippet'],
				'items' => Cart::getItemsWithModels(false),
				'total' => Cart::getTotal(),
				'aside' => true,
				'intro' => true,
			]);
	}

	public function success() {
		Cart::clear();
		return view('front.store.success')->with('aside', true);
	}

	public function push() {
		# get data
		$data = Cart::getKlarnaOrder(Input::get('klarna_order'))->marshal();

		Log::info('Klarna pushed us with data:', $data);

		# get order model
		$order = Order::where('reservation', '=', $data['reservation'])->first();
		if (!$order) {
			$order = $this->createOrder(Input::get('klarna_order'));
            Log::info("store.push: Creating order.");
		}

		# update the order with new data
		$order->klarna_status = $data['status'];
		$order->save();

		return response('OK', 200);
	}

	private function createOrder($id) {
		# get klarna order
		$data = Cart::getKlarnaOrder($id)->marshal();

		# decode the item reference JSON
		foreach($data['cart']['items'] as $key => $item) {
			$data['cart']['items'][$key]['reference'] = json_decode($item['reference'], true);
		}

		# parse data
		foreach($data['cart']['items'] as &$item) {
			$item['total_price_excluding_tax'] /= 100;
			$item['total_price_including_tax'] /= 100;
		}

		# create new order
		$order = new Order();

		$order->status = 'unprocessed';

		$order->klarna_id = $id;
		$order->items = $data['cart']['items'];
		$order->klarna_status = $data['status'];
		$order->reservation = $data['reservation'];
		$order->total_price_excluding_tax = $data['cart']['total_price_excluding_tax'] / 100;
		$order->total_price_including_tax = $data['cart']['total_price_including_tax'] / 100;
		$order->total_tax_amount = $data['cart']['total_tax_amount'];
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

		# react to sale
		foreach($order->items as $item) {
			if ($item['type'] == 'shipping_fee') continue;
			$product = Product::find($item['reference']['id']);
			if ( ! $product) continue;
			$product->sell($item['quantity'], $item['reference']['options']['variants']);
			$product->save();
		}

		# notify user
		Mail::send('emails.store.order_received', ['title' => 'Order Received', 'order' => $order], function($mail) use($user) {
			$mail->to('me@afflicto.net')->subject('Your order at ' .Store::current()->name);
		});

		return $order;
	}

}