<?php namespace Friluft\Http\Controllers\Admin;

use App;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Image;
use Illuminate\Routing\Router;
use Laratable;
use Input;
use Redirect;
use Friluft\Manufacturer;
use DB;
use Former;

class ManufacturersController extends Controller {

	public function index()
	{

		$query = Manufacturer::query();
		if ( ! Input::has('sort')) $query->orderBy('name', 'asc');

		$table = Laratable::make($query, [
			'#' => 'id',
			'Name' => 'name',
			'Forhåndsbestilling' => ['prepurchase_days', function($model) {
				if ($model->prepurchase_enabled) {
					return 'maks ' .$model->prepurchase_days .' dager';
				}else {
					return '<span class="color-error">Deaktivert</span>';
				}
			}],
		]);

		$table->sortable(true, ['id', 'name']);

		$table->editable(true, url('admin/manufacturers/{id}/edit'));
		$table->destroyable(true, url('admin/manufacturers/{id}'));

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
		$mf = new Manufacturer(Input::only('name', 'slug', 'description'));
		$mf->prepurchase_enabled = Input::has('prepurchase_enabled');
		if ($mf->prepurchase_enabled) {
			$mf->prepurchase_days = Input::get('prepurchase_days');
		}
		$mf->save();

		if (Input::hasFile('logo')) {
			$file = Input::file('logo');

			$filename = $file->getClientOriginalName();

			$file->move(public_path('images/manufacturers'), $filename);

			$image = new Image();
			$image->type = 'manufacturer';
			$image->name = $filename;

			$image->save();
			$mf->image()->associate($image);
		}

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
		$mf->description = Input::get('description', null);
		$mf->prepurchase_enabled = Input::has('prepurchase_enabled');
		if ($mf->prepurchase_enabled) {
			$mf->prepurchase_days = Input::get('prepurchase_days');
		}

		if (Input::hasFile('logo')) {
			$file = Input::file('logo');

			$filename = $file->getClientOriginalName();

			$file->move(public_path('images/manufacturers'), $filename);

			$image = $mf->image;
			if (!$image) {
				$image = new Image();
				$image->type = 'manufacturer';
			}

			$image->name = $filename;

			$image->save();
			$mf->image()->associate($image);
		}

		$mf->save();

		return Redirect::route('admin.manufacturers.index')->with('success', trans('admin.manufacturers_update_success', ['manufacturer' => $mf->name]));
	}

	public function destroy(Manufacturer $mf)
	{
		DB::table('products')->where('manufacturer_id', '=', $mf->id)->update(['manufacturer_id' => null]);

		$mf->delete();

		return Redirect::route('admin.manufacturers.index')->with('success', trans('admin.manufacturers_delete_success', ['manufacturer' => $mf->name]));
	}

}
