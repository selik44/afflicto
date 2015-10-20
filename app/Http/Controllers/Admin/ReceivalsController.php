<?php namespace Friluft\Http\Controllers\Admin;

use DB;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Product;
use Friluft\Receival;
use Input;
use Redirect;
use Response;

class ReceivalsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$table = Laratable::make(Receival::query(), [
			'#' => 'id',
			'Products' => ['products', function($model, $column, $value) {
				$str = '';
				return $str;
			}],
			'When' => 'when diffForHumans',
		]);

		$table->editable(true, url('admin/receivals/{id}/edit'));
		$table->destroyable(true, url('admin/receivals/{id}'));
		$table->sortable(true, [
			'name','price','stock','enabled','updated_at'
		]);

		return $this->view('admin.products_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
			]);
	}

	public function getVariants(Product $product) {
		if ( ! $product->hasVariants()) {
			return Response::json(false);
		}

		return $product->variants;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->view('admin.receivals_create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$receival = new Receival();
		$receival->manufacturer_id = Input::get('manufacturer_id');
		$receival->save();

		return \Redirect::route('admin.receivals.edit', [$receival]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Receival $receival)
	{
		return view('admin.receivals_edit')->with([
			'receival' => $receival,
			'products' => Product::whereManufacturerId($receival->manufacturer_id)->get(),
		]);
	}

	public function getLine(Receival $receival, Product $product) {
		return view('admin.partial.receivals_line')->with([
			'receival' => $receival,
			'product' => $product
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Receival $receival)
	{
		$receival->products = Input::get('products');
		$receival->save();

		return Response::json('OK');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		DB::table('receivals')->delete($id);
		return Redirect::route('admin.receivals.index')->with('success', 'Receival #' + $id + ' deleted!');
	}

}
