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
			'old_password' => 'required_with:password,password_confirmation',
			'password' => 'min:8|required_with:old_password|confirmed:password_confirmation'
		];
	}

}
