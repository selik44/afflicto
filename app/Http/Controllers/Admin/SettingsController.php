<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Illuminate\Http\Request;
use Input;

class SettingsController extends Controller {

	public function getDesign() {
		$image = Image::whereType('background')->first();
		\Former::populate([
			'use_background_image' => ($image) ? true : false,
		]);
		return view('admin.design_general');
	}

	public function putDesign() {
		if (Input::hasFile('background')) {
			$image = Image::whereType('background')->first();

			if ( ! $image) {
				$image = new Image();
				$image->type = 'background';
			}

			$file = Input::file('background');

			$filename = $file->getClientOriginalName();

			$file->move(public_path('images'), $filename);

			$image->name = $filename;
			$image->save();
		}else if ( ! Input::has('use_background_image')) {
			$image = Image::whereType('background')->first();
			if ($image) $image->delete();
		}

		return \Redirect::back()->with('success', 'Settings Saved!');
	}

}
