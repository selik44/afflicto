<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Illuminate\Http\Request;
use Input;

class BannersController extends Controller {

	public static $spots = [
		'top_left',
		'top_right',
		'bottom_left',
		'bottom_right',
	];

	public static function getImages() {
		$images = [];
		foreach(self::$spots as $spot) {
			$images[$spot] = Image::whereType('banners_' .$spot)->first();
		}

		return $images;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$images = self::getImages();

		return view('admin.banners_index')->with('images', $images);
	}

	/**
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		foreach(self::$spots as $spot) {
			if (Input::hasFile($spot)) {

				$file = Input::file($spot);

				$file->move(public_path('images/slides'), $file->getClientOriginalName());

				$image = Image::wheretype('banners_' .$spot)->first();
				if ( ! $image) {
					$image = new Image();
					$image->type = 'banners_' .$spot;
				}
				$image->name = 'slides/' .$file->getClientOriginalName();
				$image->save();
			}
		}

		return \Redirect::back()->with('success', 'Banners updated!');
	}


}
