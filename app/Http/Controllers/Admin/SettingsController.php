<?php namespace Friluft\Http\Controllers\Admin;

use Former;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Image;
use Friluft\Setting;
use Input;

class SettingsController extends Controller {

	private $settings = [
		'meta_description',
		'meta_keywords'
	];

	public function index() {
		# populate
		$fields = [];
		foreach($this->settings as $setting) {
			$setting = Setting::whereMachine($setting)->first();

			Former::populate([
				$setting->machine => $setting->value,
			]);

			$fields[] = $setting->getField();
		}

		return view('admin.settings_index')->with([
			'fields' => $fields,
		]);
	}

	public function update() {
		foreach($this->settings as $setting) {
			$setting = Setting::whereMachine($setting)->first();

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
