<?php namespace Friluft\Http\Requests;

use Friluft\Http\Requests\Request;

class CreateManufacturerRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|max:255',
			'slug' => 'required|slug|max:255',
		];
	}

}
