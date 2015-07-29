<?php namespace Friluft\Http\Controllers\Admin;

use Former;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Product;
use Friluft\Variant;
use Illuminate\Http\Request;
use Input;
use Laratable;
use Redirect;

class VariantsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$table = Laratable::make(Variant::query(), [
			'#' => 'id',
			'Name' => 'admin_name',
			'Display Name' => 'name',
			'Values' => ['data', function($variant) {
				$values = [];
				foreach($variant->data['values'] as $value) {
					$values[] = $value['name'];
				}

				return implode(', ', $values);
			}],
		]);

		$table->editable(true, url('admin/variants/{id}/edit'));
		$table->destroyable(true, url('admin/variants/{id}'));

		$table->filterable(true);
		$table->addFilter('name', 'search');
		$table->paginate(true, 13);

		return $this->view('admin.variants_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
				'filters' => $table->buildFilters()->addClass('inline')
			]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.variants_create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$variant = new Variant();
		$variant->data = ['values' => [], 'uid' => 0];
		$variant->name = Input::get('name');
		$variant->admin_name = Input::get('admin_name');
		$variant->save();

		return \Redirect::route('admin.variants.index');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Variant $variant)
	{
		Former::populate($variant);
		return view('admin.variants_edit')->with('variant', $variant);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Variant $variant)
	{
		$variant->name = Input::get('name');
		$variant->admin_name = Input::get('admin_name');

		# update current values
		$data = $variant->data;
		foreach($data['values'] as $key => $value) {
			if ( ! Input::has('variant-' .$value['id'])) {
				unset($data['values'][$key]);
			}else {
				$data['values'][$key]['name'] = Input::get('variant-' .$value['id']);
			}
		}

		# add new ones?
		if (Input::has('values')) {
			foreach(Input::get('values') as $name) {
				$data['values'][] = ['id' => ++$data['uid'], 'name' => $name];
			}
		}

		$variant->data = $data;
		$variant->save();

		return Redirect::route('admin.variants.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Variant $variant)
	{
		$variant->delete();

		return Redirect::route('admin.variants.index');
	}

}
