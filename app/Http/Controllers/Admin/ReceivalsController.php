<?php namespace Friluft\Http\Controllers\Admin;

use DB;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Manufacturer;
use Friluft\Product;
use Friluft\Receival;
use Friluft\Utils\LocalizedCarbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
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
				return numberFormat($model->getSum()) .',-';
			}],
			'Forventet Ankomst' => ['expected_arrival', function($model) {
				return LocalizedCarbon::diffForHumans($model->expected_arrival, null, true);
			}],
			'Rest' => ['rest', function($model) {
				return ($model->parent != null) ? '<span class="color-success">For #' .$model->parent->id .'</span>' : '<span class="color-error">Nei</span>';
			}],
			'Motatt' => ['received', function($model) {
				return ($model->received) ? '<span class="color-success">' .$model->arrived_at->diffForHumans() .'</span>' : '<span class="color-error">Nei</span>';
			}],
			'Oppdatert' => 'updated_at diffForHumans',
			'' => ['_actions', function($model) {
				return '<div class="button-group actions">
					<a class="button small primary" title="Edit" href="' .route('admin.receivals.edit', $model) .'"><i class="fa fa-search"></i> Edit</a>
					<a class="button small" title="Packlist" href="' .route('admin.receivals.packlist', $model) .'"><i class="fa fa-download"></i> Packlist</a>
					<a class="button small success" title="Mottak" href="' .route('admin.receivals.receive', $model) .'"><i class="fa fa-check"></i> Mottak</a>
					<form method="POST" action="' .route('admin.receivals.destroy', $model) .'">
						<input type="hidden" name="_method" value="DELETE">
						<input type="hidden" name="_token" value="' .csrf_token() .'">
						<button title="Delete" class="error small"><i class="fa fa-trash"></i> Trash</button>
					</form>
				</div>';
			}],
		]);

		# set sortable
		$table->sortable(true, [
			'id','expected_arrival','rest','received','updated_at',
		]);

		# set filterable
		$table->filterable(true);

		#------------ add manufacturers filter------------

		# get all the manufacturers who appear in any receivals
		$manufacturers = new Collection();
		foreach(Receival::all(['manufacturer_id']) as $receival) {
			$id = $receival['manufacturer_id'];
			if ( ! isset($manufacturers[$id])) {
				$manufacturers[$id] = Manufacturer::whereId($id)->get(['id', 'name'])->first();
			}
		}

		# sort by name
		$manufacturers = $manufacturers->sortBy('name');

		# create the filter
		$mfs = ['*' => 'Any'];
		foreach($manufacturers as $mf) {
			$mfs[$mf->id] = $mf->name;
		}
		$mfFilter = $table->addFilter('manufacturer_id', 'select');
		$mfFilter->setValues($mfs);
		$mfFilter->setLabel("Produsent");

		# add "rest" filter
		$restFilter = $table->addFilter('id', 'boolean');
		$restFilter->setLabels('Ja', 'Nei', 'Alle');
		$restFilter->setLabel('Rest');
		$restFilter->setFilterFunction(function($filter, $query) {
			$value = $filter->getValue();
			if ($value != 'any') {
				if ($value == '1') {
					$query->whereNotNull('receival_id');
				}else {
					$query->whereNull('receival_id');
				}
			}
		});

		# add "received" filter
		$receivedFilter = $table->addFilter('received', 'boolean');
		$receivedFilter->setLabels('Ja', 'Nei', 'Alle');
		$receivedFilter->setLabel('Mottatt');

		return $this->view('admin.receivals_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
				'filters' => $table->buildFilters()->addClass('inline'),
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

		return Redirect::route('admin.receivals.edit', [$receival]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Receival $receival
	 * @return Response
	 * @internal param int $id
	 */
	public function edit(Receival $receival)
	{
		return view('admin.receivals_edit')->with([
			'receival' => $receival,
			'products' => Product::whereManufacturerId($receival->manufacturer_id)->withoutCompounds()->get(),
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
			'user' => \Auth::user(),
		]);
	}

	public function getReceive(Receival $receival) {

		if ($receival->received) return Redirect::back()->with('error', 'Det varemottaket har allerede blitt mottatt.');

		return view('admin.receivals_receive')->with([
			'receival' => $receival,
		]);
	}

	public function putReceive(Receival $receival) {
		# prevent applying several times
		if ($receival->received) return Redirect::back()->with('error', 'Det varemottaket har allerede blitt utført.');

		# update the receival with the received numbers and save it
		$receival->receive(Input::all());
		$receival->save();

		# generate a "rest" based on the difference.
		$rest = $receival->generateRest();

		if ($rest != null) {
			return Redirect::route('admin.receivals.edit', $rest)->with('warning', 'De manglende enhetene har blitt samlet til dette rest-varemottaket.');
		}else {
			return Redirect::route('admin.receivals.index')->with('success', 'Varemottaket er utført.');
		}
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