<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Friluft\Category;
use Friluft\Product;
use Cart;
use Klarna_Checkout_Order;
use Klarna_Checkout_Connector;
use Log;
use Input;

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
				'items' => Cart::getItemsWithModels(true),
				'total' => Cart::getTotal(),
			]);
	}

	/**
	 * Shows the checkout form
	 * @return View
	 */
	public function checkout() {

		# create the Klarna Cart
		$create = ['cart' => ['items' => []]];

		# Add the products
		foreach(Cart::getItemsWithModels(false) as $item) {
			$create['cart']['items'][] = [
				'reference' => $item['model']->id,
				'name' => $item['model']->name,
				'quantity' => $item['quantity'],
				'unit_price' => $item['model']->price * 100,
				'discount_rate' => 0,
				'tax_rate' => $item['model']->tax_percentage * 100,
			];
		}

		# add shipping costs
		$create['cart']['items'][] = [
			'type' => 'shipping_fee',
			'reference' => 'SHIPPING',
			'name' => 'Shipping Fee',
			'quantity' => 1,
			'unit_price' => 4900,
			'tax_rate' => 2500,
		];

		# configure checkout
		$create['purchase_country'] = 'NO';
		$create['purchase_currency'] = 'NOK';
		$create['locale'] = 'nb-no';
		$create['merchant'] = [
			'id' => getenv('KLARNA_MERCHANT_ID'),
			'terms_uri' => url('terms-and-conditions'),
			'checkout_uri' => url('store/checkout'),
			'confirmation_uri' => url('store/success'),
			'push_uri' => url('store/push'),
		];

		#init klarna
		Klarna_Checkout_Order::$baseUri = getenv('KLARNA_URI');
		Klarna_Checkout_Order::$contentType = getenv('KLARNA_CONTENT_TYPE');

		# get klarna connector
		$connector = Klarna_Checkout_Connector::create(getenv('KLARNA_SHARED_SECRET'));

		# create a new order
		$order = new Klarna_Checkout_Order($connector);
		$order->create($create);

		# fetch
		$order->fetch();

		return view('front.store.checkout')
		->with('snippet', $order['gui']['snippet']);
	}

	public function success() {
		return view('front.store.success');
	}

	public function push() {
		\Log::debug("------Klarna push----------");
		foreach(\Input::all() as $key => $value) {
			\Log::debug($key .' => ' .$value);
		}
	}

}
