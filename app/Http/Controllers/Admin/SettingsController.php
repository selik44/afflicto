<?php namespace Friluft\Http\Controllers\Admin;

use Former;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Friluft\Setting;
use Illuminate\Http\Request;
use Input;

class SettingsController extends Controller {

	private $settings = [
		'slogan_background',
		'slogan_color',
		'slogan_content',
		'store_slogan_1_content',
		'store_slogan_2_content',
		'store_slogan_3_content',
		'store_slogan_4_content',
		'footer_1_content',
		'footer_2_content',
		'footer_3_content',
		'checkout_1_content',
		'checkout_2_content',
		'checkout_3_content',
	];

	public function getDesign() {
		# background image
		$image = Image::whereType('background')->first();

		# populate
		$fields = [];
		foreach($this->settings as $setting) {
			$setting = Setting::whereMachine($setting)->first();

			Former::populate([
				$setting->machine => $setting->value,
			]);

			$fields[] = $setting->getField();
		}

		Former::populate([
			'use_background_image' => ($image) ? true : false,
		]);
		return view('admin.design_general')->with([
			'fields' => $fields,
		]);
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

		# save settings
		foreach(Setting::all() as $setting) {
			if ($setting->type == 'boolean') {
				$setting->value = Input::has($setting->machine);
			}else {
				$setting->value = Input::get($setting->machine, '');
			}

			$setting->save();
		}

		return \Redirect::back()->with('success', 'Settings Saved!');
	}

}
