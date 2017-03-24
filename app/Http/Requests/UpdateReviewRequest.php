<?php namespace Friluft\Http\Requests;

use Friluft\Http\Requests\Request;
use Friluft\Review;

class UpdateReviewRequest extends Request {

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
		return Review::$rules;
	}

}
