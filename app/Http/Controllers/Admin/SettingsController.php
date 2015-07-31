<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Illuminate\Http\Request;
use Input;

class SettingsController extends Controller {

	public function getDesign() {
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
		}

		return \Redirect::back()->with('success', 'Settings Saved!');
	}

}
