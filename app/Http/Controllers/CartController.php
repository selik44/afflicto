<?php namespace Friluft\Http\Controllers;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Cart;
use Input;
use Request;
use Redirect;

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
			return Cart::getItemsWithModels(true);
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

		$product = Input::get('product_id');
		$quantity = Input::get('quantity', 1);

		$id = ['id' => Cart::add($product, $quantity)];

		return (Request::wantsJson()) ? $id : Redirect::back()->with('success', 'added ' .$product .' to cart.');
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

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if (!Cart::has($id)) return response("error", 404);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (Cart::has($id)) Cart::remove($id);
		return response("OK");
	}

}
