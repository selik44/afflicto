<?php namespace Friluft\Http\Controllers\Admin;

use App;
use Former\Facades\Former;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Illuminate\Routing\Router;
use Laratable;
use Input;
use Redirect;
use Friluft\Manufacturer;

class ManufacturersController extends Controller {

	public function index()
	{
		$table = Laratable::make(Manufacturer::query(), [
			'#' => 'id',
			'Name' => 'name',
		]);

		$table->sortable(true, ['id', 'name']);

		$table->editable(true, url(App::getLocale() .'/admin/manufacturers/{id}/edit'));
		$table->destroyable(true, url(App::getLocale() .'/admin/manufacturers/{id}'));

		return $this->view('admin.manufacturers_index')->with([
			'table' => $table->render(),
			'pagination' => $table->paginator
		]);
	}

	public function create()
	{
		return $this->view('admin.manufacturers_create')->with([
			'form' => form('admin.manufacturer')
		]);
	}

	public function store(Requests\CreateManufacturerRequest $request)
	{
		$mf = new Manufacturer(Input::only('name', 'slug'));
		$mf->save();

		return Redirect::route('admin.manufacturers.index')
			->with('success', trans('admin.manufacturers_create_success', ['manufacturer' => $mf->name]));
	}

	public function edit(Manufacturer $mf)
	{
		Former::populate($mf);

		return $this->view('admin.manufacturers_edit')->with([
			'form' => form('admin.manufacturer'),
			'manufacturer' => $mf,
		]);
	}

	public function update(Manufacturer $mf)
	{
		$mf->name = Input::get('name');
		$mf->slug = Input::get('slug');
		$mf->save();

		return Redirect::route('admin.manufacturers.index')->with('success', trans('admin.manufacturers_update_success', ['manufacturer' => $mf->name]));
	}

	public function destroy(Manufacturer $mf)
	{
		$mf->delete();

		return Redirect::route('admin.manufacturers.index')->with('success', trans('admin.manufacturers_delete_success', ['manufacturer' => $mf->name]));
	}

}
