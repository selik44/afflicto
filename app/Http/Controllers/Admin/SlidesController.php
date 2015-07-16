<?php namespace Friluft\Http\Controllers\Admin;

use DB;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Illuminate\Http\Request;
use Input;

class SlidesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('admin.slides_index')->with('slides', Image::where('type', '=', 'slideshow')->orderBy('order', 'asc')->get());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.slides_create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$file = Input::file('file');

		$filename = $file->getClientOriginalName();

		if ($file->move(public_path('images/slides'), $filename)) {
			$image = new Image();
			$image->type = 'slideshow';
			$image->name = 'slides/' .$filename;
			$image->save();

			return response('OK', 200);
		}

		return response('ERROR', 500);
	}

	public function order() {
		if (!Input::has('order')) return response('ERROR: Invalid input.', 400);
		$order = json_decode(Input::get('order'), true);
		foreach($order as $image) {
			DB::table('images')
				->where('id', '=', $image['id'])
				->update(['order' => $image['order']]);
		}
		return response('OK', 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Image $image)
	{
		return $image;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Image $image)
	{
		$image->data = json_decode(Input::get('data', ''), true);
		$image->save();
		return response('OK', 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Image $image)
	{
		$image->delete();
		return response('OK', 200);
	}

}
