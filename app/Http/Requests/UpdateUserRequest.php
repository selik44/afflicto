<?php namespace Friluft\Http\Requests;

use Friluft\Http\Requests\Request;

class UpdateUserRequest extends Request {

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
			'firstname' => 'required|max:60',
			'lastname' => 'max:60',
			'email' => 'required|email|unique:users',
			'role_id' => 'required',
		];
	}

}
