<?php namespace Friluft\Http\Controllers\Admin;

use DB;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Product;
use Friluft\Receival;
use Input;
use Laratable;
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
			'manufacturer' => 'manufacturer->name',
			'Ordresum' => ['_ordersum', function($model) {
				return $model->getSum() .',-';
			}],
			'Date' => 'expected_arrival diffForHumans',
			'Rest' => ['rest', function($model) {
				return ($model->rest) ? '<span class="color-success">Ja</span>' : '<span class="color-error">Nei</span>';
			}],
			'Motatt' => ['received', function($model) {
				return ($model->received) ? '<span class="color-success">Ja</span>' : '<span class="color-error">Nei</span>';
			}],
			'Oppdatert' => 'updated_at diffForHumans',
			'' => ['_actions', function($model) {
				return '<div class="button-group actions">
					<a class="button small primary" title="Edit" href="' .route('admin.receivals.edit', $model) .'"><i class="fa fa-search"></i> Edit</a>
					<a class="button small" title="Packlist" href="' .route('admin.receivals.packlist', $model) .'"><i class="fa fa-download"></i> Packlist</a>
					<a class="button small success" title="Mottak" href="' .route('admin.receivals.receive', $model) .'"><i class="fa fa-check"></i> Mottak</a>
					<form method="POST" action="' .route('admin.orders.delete', $model) .'">
						<input type="hidden" name="_method" value="DELETE">
						<input type="hidden" name="_token" value="' .csrf_token() .'">
						<button title="Delete" class="error small"><i class="fa fa-trash"></i> Trash</button>
					</form>
				</div>';
			}],
		]);

		$table->sortable(true, [
			'id','expected_arrival','rest','received',
		]);

		return $this->view('admin.receivals_index')
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

	public function getPacklist(Receival $receival) {
		return view('admin.receivals_packlist')->with([
			'receivals' => [$receival],
		]);
	}

	public function getReceive(Receival $receival) {

		if ($receival->received) return Redirect::back()->with('error', 'Det varemottaket har allerede blitt mottatt.');

		return view('admin.receivals_receive')->with([
			'receival' => $receival,
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
		$receival->expected_arrival = Input::get('expected_arrival');
		$receival->save();
		return Response::json('OK');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Receival $r)
	{
		$r->delete();
		return Redirect::route('admin.receivals.index')->with('success', 'Receival #' + $r->id + ' deleted!');
	}

}