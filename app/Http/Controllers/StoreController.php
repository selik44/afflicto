<?php namespace Friluft\Http\Controllers;

use Agent;
use Friluft\Http\Requests;
use Friluft\Order;
use Friluft\Page;
use Friluft\Role;
use Friluft\User;
use Friluft\Category;
use Friluft\Product;
use Friluft\Store;
use Cart;
use Klarna;
use Log;
use Input;
use Mail;
use Mailchimp;
use Session;

class StoreController extends Controller {

	/**
	 * @var Mailchimp
	 */
	private $mailchimp;

	public function __construct(Mailchimp $mailchimp) {
		$this->mailchimp = $mailchimp;
	}

	/**
	 * @return Klarna
	 */
	public function makeKlarna() {
		$k = new Klarna();
		$k->config(
			env('KLARNA_MERCHANT_ID'),
			env('KLARNA_SHARED_SECRET'),
			\KlarnaCountry::NO,
			\KlarnaLanguage::NB,
			\KlarnaCurrency::NOK,
			Klarna::BETA,
			'json',
			base_path('resources/pclasses.json')
		);

		return $k;
	}

	public function index($path) {
		$page = Page::whereSlug($path)->first();
		if ($page) {
			return view('front.page')
				->with([
					'page' => $page,
					'aside' => $page['options']['sidebar']
				]);
		}

		$path = explode('/', $path);
		$tree = [];
		$parent_id = null;
		$product = null;
		foreach($path as $slug) {
			$cat = Category::whereParentId($parent_id)->whereSlug($slug)->first();
			if ($cat) {
				$tree[] = $cat;
				$parent_id = $cat->id;
			}else {
				$tree[] = Product::where('categories', 'LIKE', '%' .$parent_id .'%')->whereSlug($slug)->first();
			}
		}

		$last = array_pop($tree);
		if ($last instanceof Product) {
			#--- product ---#
			$product = $last;

			$category = array_pop($tree);

			return view('front.store_product')
				->with([
					'category' => $category,
					'product' => $product,
					'aside' => true
				]);
		}else if ($last instanceof Category) {
			#--- category ---#
			$cat = $last;
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
					if ($variant->filterable) {
						$name = strtolower(str_replace(' ', '-', $variant->name));
						if ( ! isset($variants[$name])) $variants[$name] = [];
						$variants[$name] = array_unique(array_merge($variants[$name], array_column($variant->data['values'], 'name')));
					}
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

		abort(404);
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
				'shipping' => Cart::getShipping(),
				'total' => Cart::getTotal(),
				'aside' => false,
			]);
	}

	public function setSubscribe($subscribe = 0) {
		if ($subscribe == 1) {
			Session::set('klarna_subscribe', 1);
			return response('Enabled');
		}else {
			Session::forget('klarna_subscribe');
			return response('Disabled');
		}
	}

	public function success() {
		if ( ! Session::has('klarna_order')) return \Redirect::route('home');

		# get order
		$order = Cart::getKlarnaOrder(Session::get('klarna_order'));

		# subscribe?
		if (Session::has('klarna_subscribe')) {
			if (\Auth::user()) {
				$email = \Auth::user()->email;
			}else {
				$email = $order['billing_address']['email'];
			}

			# subscribe to newsletter
			$this->mailchimp
				->lists
				->subscribe(env('MAILCHIMP_NEWSLETTER_ID'), ['email' => $email]);

			# clean up
			Session::forget('klarna_subscribe');
		}

		if (Agent::isMobile()) {
			$data['options']['gui']['layout'] = 'mobile';
		}

		# get the gui snippet
		$snippet = $order['gui']['snippet'];

		# clear the cart
		Cart::clear();

		return view('front.store.success')->with([
			'aside' => true,
			'snippet' => $snippet,
		]);
	}

	public function push() {
		# get data
		$data = Cart::getKlarnaOrder(Input::get('klarna_order'));

		Log::info('Klarna pushed us with data:', $data->marshal());

		# get order model
		$order = Order::where('reservation', '=', $data['reservation'])->first();

		# create the order?
		if ( ! $order) {
			Log::info("store.push: Creating order.");
			$order = $this->createOrder(Input::get('klarna_order'));
		}

		# update the order with new data
		$order->klarna_status = $data['status'];

		# save order
		$order->save();

		# update order id
		$klarna = $this->makeKlarna();
		$klarna->setEstoreInfo('' .$order->id);
		$klarna->update($order->reservation, true);

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
			$item['total_tax_amount'] /= 100;
			$item['tax_rate'] /= 100;
			$item['unit_price'] /= 100;
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
		$order->total_tax_amount = $data['cart']['total_tax_amount'] / 100;
		$order->billing_address = $data['billing_address'];
		$order->shipping_address = $data['shipping_address'];
		$order->locale = $data['locale'];
		$order->purchase_country = $data['purchase_country'];
		$order->purchase_currency = $data['purchase_currency'];

		$email = $data['billing_address']['email'];
		$user = null;

		$custom = json_decode($data['merchant_reference']['orderid2'], true);
		if (isset($custom['user_id'])) {
			$user = User::find($custom['user_id']);
			$email = $user->email;
		}else {
			$user = User::whereEmail($email)->first();
		}

		# create a new user automatically?
		if ( ! $user) {
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
			$user->password = \Hash::make($password);

			# set phone, billing address & shipping address
			$user->phone = $order->billing_address['phone'];
			$user->shipping_address = $order->shipping_address;
			$user->billing_address = $order->billing_address;

			# save
			$user->save();

			# welcome the user
			Mail::send('emails.store.welcome', ['email' => $user->email, 'password' => $password], function($mail) use($user) {
				$mail->to($user->email)->subject(trans('emails.welcome.subject', ['store' => Store::current()->name]));
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
		}

		# notify user
		Mail::send('emails.store.order_confirmation', ['order' => $order], function($mail) use($user, $order) {
			$mail->to($user->email)->subject(trans('emails.order_confirmation.subject', ['id' => $order->id]));
		});

		return $order;
	}

}