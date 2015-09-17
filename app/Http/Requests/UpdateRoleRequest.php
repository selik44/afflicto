<?php namespace Friluft\Http\Requests;

use Friluft\Http\Requests\Request;

class UpdateRoleRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		if (!\Auth::check()) return false;

		$user = \Auth::user();
		$role = $user->role;
		if (!$role) return false;

		return false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|max:255'
		];
	}

}
