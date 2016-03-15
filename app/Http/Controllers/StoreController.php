<?php namespace Friluft\Http\Controllers;

use Agent;
use Auth;
use Former;
use Friluft\Coupon;
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
			$content = $page->content;
			$aside = $page['options']['sidebar'] ? true : false;

			if (Auth::user()) {
				$user = Auth::user();
				Former::populate($user);
			}

			if ($page->slug == 'kontakt-oss') {
				$content = str_replace('{{form}}', view('front.partial.contact-form')->render(), $content);
			}else if ($page->slug == 'bytte-og-retur') {
				$content = str_replace('{{form}}', view('front.partial.retur-form')->render(), $content);
			}else if ($page->slug == 'samarbeid') {
				$content = str_replace('{{form}}', view('front.partial.partners-form')->render(), $content);
			}

			return view('front.page')
				->with([
					'content' => $content,
					'page' => $page,
					'aside' => $aside
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

			if ($category == null) {
				abort(404);
			}

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
				'coupons' => Cart::getCoupons(),
				'saved' => Cart::getAmountSaved(),
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
			try {
				$this->mailchimp
					->lists
					->subscribe(env('MAILCHIMP_NEWSLETTER_ID'), ['email' => $email], null, null, false);
			}catch (\Exception $e) {
				Log::error('Cannot subscribe to newsletter: ' .$e->getMessage());
			}

			# clean up
			Session::forget('klarna_subscribe');
		}

		if (Agent::isMobile()) {
			$data['options']['gui']['layout'] = 'mobile';
		}

		# get the gui snippet
		$snippet = $order['gui']['snippet'];

		# get the items in the cart
		$items = Cart::getItemsWithModels();
		$total = Cart::getTotal();
		$weight = Cart::getTotalWeight();
		$revenue = Cart::getRevenue();
		$shipping = Cart::getShipping()['unit_price'] / 100;
		$tax = Cart::getTotalTax();
		$id = Session::get('klarna_order');

		# clear the cart
		Cart::clear();

		return view('front.store.success')->with([
			'aside' => true,
			'snippet' => $snippet,
			'items' => $items,
			'total' => $total,
			'weight' => $weight,
			'id' => $id,
			'revenue' => $revenue,
			'shipping' => $shipping,
			'tax' => $tax,
		]);
	}

	public function push() {
		# get data
		$data = Cart::getKlarnaOrder(Input::get('klarna_order'));

		Log::info('Klarna pushed us with data:', $data->marshal());

		# is it checkout_complete ?
		if ($data['status'] == 'checkout_complete') {
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

			# update klarna order with 'created' status and orderid
			$data->update([
				'status' => 'created',
				'merchant_reference' => [
					'orderid1' => '' .$order->id
				],
			]);
		}else {
			return response('OK', 200);
		}

		return response('OK', 200);
	}

	private function createOrder($id) {
		Log::info('creating order. klarna_order_id: ' .$id);

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

		#--------- get user & coupon---------#
		$user = null;
		$coupon = null;

		# get custom data
		if (isset($data['merchant_reference']) && isset($data['merchant_reference']['orderid2'])) {
			$custom = json_decode($data['merchant_reference']['orderid2'], true);

			# get user data from klarna order data?
			if (isset($custom['user_id'])) {
				Log::debug('Finding user from ID: ' .$custom['user_id']);
				$user = User::find($custom['user_id']);
				if ($user) {
					Log::debug('got user from merchant_reference. ID: ' .$user->id);
				}else {
					Log::debug('Nope');
				}
			}
		}

		# otherwise, get it from billing_address
		if ( ! $user) {
			Log::debug('getting user from data.billing_address.email...');
			$user = User::withTrashed()->where('email', '=', $data['billing_address']['email'])->first();
			if ($user) {

				# re-activate user?
				if ($user->deleted_at != null) {
					$user->deleted_at = null;
					$user->save();
				}
				Log::debug('got user from billing email.');
			}else {
				Log::debug('nope');
			}
		}

		# create a new user automatically?
		if ( ! $user) {
			Log::debug('Creating user with email: ' .$data['billing_address']['email']);
			$user = new User();
			$user->role()->associate(Role::where('machine', '=', 'regular')->first());
			$user->email = $data['billing_address']['email'];

			# parse name
			$name = explode(' ', $data['billing_address']['given_name']);
			if (count($name) > 1) {
				$firstname = '';
				$lastname = '';
				foreach($name as $key => $segment) {
					if ($key >= count($name) - 1) {
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
			Mail::send('emails.store.welcome', ['email' => $user->email, 'password' => $password, 'id' => $user->id], function($mail) use($user) {
				$mail->to($user->email)->subject(trans('emails.welcome.subject', ['store' => Store::current()->name]));
				$mail->to('me@afflicto.net')->subject('staging: ' .trans('emails.welcome.subject', ['store' => Store::current()->name]));
			});
		}

		# associate user with order
		$order->user()->associate($user);

		# save the order
		$order->save();

		# react to sale
		foreach($order->items as $item) {
			if ($item['type'] == 'shipping_fee') continue;
			$product = Product::find($item['reference']['id']);
			if ( ! $product) continue;
			$product->sell($item['quantity'], $item['reference']['options']['variants']);
		}

		# associate with coupons
		if (isset($custom) && isset($custom['coupons'])) {
			foreach($custom['coupons'] as $code) {
				$coupon = Coupon::whereCode($code)->first();

				if ($coupon) {
					$order->coupons()->attach($coupon);

					# is the coupon single_use? if so store this usage to prevent further use.
					if ($coupon->single_use) $user->coupons()->attach($coupon);
				}
			}
		}

		# notify user
		Mail::send('emails.store.order_confirmation', ['order' => $order], function($mail) use($user, $order) {
			$mail->to($user->email)->subject('Ordrebekreftelse #' .$order->id);

			# staging?
			if (env('APP_ENV') == 'staging') {
				$mail->to('petter@gentlefox.net')->subject('staging: Ordrebekreftelse #' .$order->id);
			}

			# live?
			if (env('APP_ENV') == 'production') {
				$mail->to('ordre@123friluft.no')->subject('Ordrebekreftelse #' .$order->id);
			}
		});

		return $order;
	}

}