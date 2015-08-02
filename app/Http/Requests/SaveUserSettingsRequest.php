<?php namespace Friluft\Http\Requests;

use Friluft\Http\Requests\Request;

class SaveUserSettingsRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return \Auth::user();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'email' => 'required|email',
			'new_password' => 'min:8|confirmed:password_confirmation',
			'password_confirmation' => 'required_with:password,password_confirmation',
			'old_password' => 'required_with:password,password_confirmation',
		];
	}

}
