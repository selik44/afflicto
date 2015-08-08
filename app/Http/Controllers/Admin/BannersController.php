<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Illuminate\Http\Request;
use Input;

class BannersController extends Controller {

	public static $spots = [
		'top_1',
		'top_2',
		'middle_top_left',
		'middle_top_right',
		'middle_bottom_left_1',
		'middle_bottom_left_2',
		'middle_bottom_right_1',
		'middle_bottom_right_2',
		'bottom_left',
		'bottom_right_1',
		'bottom_right_2',
		'bottom',
	];

	public static function getImages() {
		$images = [];
		foreach(self::$spots as $spot) {
			$img = Image::whereType('banners_' .$spot)->first();
			if (!$img) {
				$img = Image::create([
					'type' => 'banners_' .$spot,
				]);
				$img->save();
			}
			$images[$spot] = $img;
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

			$image = Image::whereType('banners_' .$spot)->first();

			if (Input::hasFile($spot) || $image) {
				if ( ! $image) {
					$image = new Image();
					$image->type = 'banners_' .$spot;
				}

				# new image file?
				if (Input::hasFile($spot)) {
					$file = Input::file($spot);
					$file->move(public_path('images/slides'), $file->getClientOriginalName());
					$image->name = 'slides/' .$file->getClientOriginalName();
				}

				# get data
				$data = $image->data;
				if ( ! is_array($data)) {
					$data = [
						'link' => '#'
					];
				}

				# link?
				if (Input::has($spot .'_link')) {
					$data['link'] = Input::get($spot .'_link');
				}

				# set data & save
				$image->data = $data;
				$image->save();
			}
		}

		return \Redirect::back()->with('success', 'Banners updated!');
	}


}
