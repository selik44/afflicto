<?php namespace Friluft\Http\Controllers;

use Auth;
use Friluft\Coupon;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Cart;
use Input;
use Request;
use Redirect;
use Friluft\Product;

class CartController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('front.partial.cart-table')->with([
			'items' => Cart::getItemsWithModels(false),
			'total' => Cart::getTotal(),
			'withCheckoutButton' => true,
			'shipping' => Cart::getShipping(),
			'withShipping' => (Input::get('withShipping', 'false') == 'true'),
			'withTotal' => (Input::get('withTotal', 'false') == 'true')
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if (!Input::has('product_id')) {
			return response("error", 300);
		}

		# get the product model
		$product = Product::find(Input::get('product_id'));

		# get quantity
		$quantity = Input::get('quantity', 1);

		# set the variants options array
		$options = [
			'variants' => []
		];

		# set variants
		foreach($product->variants as $variant) {
			if (!Input::has('variant-' .$variant->id)) {
				return response('Variant ' .$variant->name .' is missing!');
			}

			$options['variants'][$variant->id] = Input::get('variant-' .$variant->id);
		}
		
		# get stock
		$stock = $product->getStock($options['variants']);

		# is quantity greater than stock?
		$totalQuantity = $quantity;

		# is there a duplicate of this product with the same variant options?
		$duplicate = Cart::getItemLike($product->id, $options);
		if ($duplicate) $totalQuantity += $duplicate['quantity'];

		if ($totalQuantity > $stock) {
			# allow always order?
			$manufacturer = $product->manufacturer;
			if ( ! $manufacturer || ! $manufacturer->always_allow_orders) {
				return response('Not enough in stock', 400);
			}
		}

		# add it to the cart
		$cartid = Cart::add($product, $quantity, $options);

		# update the klarna order
		Cart::updateKlarnaOrder();

		# return some data
		return ['id' => $cartid, 'total' => Cart::getTotal(), 'quantity' => Cart::quantity()];
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (!Cart::has($id)) return response("error", 404);

		return Cart::get($id);
	}

	public function destroy($id)
	{
		Cart::remove($id);
		Cart::updateKlarnaOrder();
		return ['total' => Cart::getTotal(), 'quantity' => Cart::quantity()];
	}

	public function setQuantity($id) {
		Cart::setQuantity($id, (int) Input::get('quantity', 0));
		Cart::updateKlarnaOrder();
		return ['total' => Cart::getTotal(), 'quantity' => Cart::quantity()];
	}

	public function clear() {
		Cart::clear();
		return Redirect::to('/')->with('success', 'Your cart has been cleared.');
	}

	public function setCouponCode($code) {
		if ( ! Auth::user()) {
			return response('unauthorized', 200);
		}

		if (Cart::hasCoupon()) {
			Cart::removeCoupon();
		}

		if (Cart::setCoupon($code)) {
			return Cart::getCoupon();
			return response('OK', 200);
		}

		return response("invalid code", 200);
	}

}
