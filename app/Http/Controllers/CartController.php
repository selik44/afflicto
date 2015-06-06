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
		# json?
		if (Request::wantsJson()) {
			return Cart::getItemsWithModels(false);
		}
		
		return view('front.cart.index')
			->with([
				'items' => Cart::getItemsWithModels(),
				'total' => Cart::getTotal(),
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
			return (Request::wantsJson()) ? response("error", 300) : Redirect::back()->with('error', 'Something went wrong there. Try again, or contact support if the error persists.');
		}

		# get the product model
		$product = Product::find(Input::get('product_id'));

		# get quantity
		$quantity = Input::get('quantity', 1);

		# set the variants options array
		$options = [
			'variants' => []
		];

		foreach($product->variants as $variant) {
			$name = $variant->name;
			if (!Input::has('variant-' .$variant->id)) {
				return (Request::wantsJson()) ? response('Variant ' .$name .' is missing!') : Redirect::back()->with('error', 'Variant ' .$name .' is missing!');
			}

			$value = Input::get('variant-' .$variant->id);
			$options['variants'][$variant->id] = $value;
		}

		$cartid = Cart::add($product, $quantity, $options);

		Cart::updateKlarnaOrder();

		return (Request::wantsJson()) ? ['id' => $cartid] : Redirect::back()->with('success', 'added ' .$product->name .'(' .$quantity .') to cart.');
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
		return response("OK");
	}

	public function setQuantity($id) {
		Cart::setQuantity($id, (int) Input::get('quantity', 0));
		Cart::updateKlarnaOrder();
		return response('OK');
	}

}
