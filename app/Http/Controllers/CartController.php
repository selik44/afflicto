<?php namespace Friluft\Http\Controllers;

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
		]);

		/*
		# json?
		if (Request::wantsJson()) {
			return Cart::getItemsWithModels(false);
		}

		return view('front.cart.index')
			->with([
				'items' => Cart::getItemsWithModels(),
				'total' => Cart::getTotal(),
			]);
		*/
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
			$name = $variant->name;
			if (!Input::has('variant-' .$variant->id)) {
				return response('Variant ' .$name .' is missing!');
			}

			$id = (int) Input::get('variant-' .$variant->id);
			foreach($variant->data['values'] as $value) {
				if ($value['id'] == $id) {
					$options['variants'][$variant->id] = $value['name'];
				}
			}
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
		return ['id' => $cartid, 'total' => Cart::getTotal()];
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
		return ['total' => Cart::getTotal()];
	}

	public function setQuantity($id) {
		Cart::setQuantity($id, (int) Input::get('quantity', 0));
		Cart::updateKlarnaOrder();
		return ['total' => Cart::getTotal()];
	}

	public function clear() {
		Cart::clear();
		return response('OK');
	}

}
