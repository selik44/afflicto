<?php

namespace Friluft\Http\Requests\Admin;

use Friluft\Http\Requests\Request;

class EditCouponRequest extends Request
{
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
			'admin_name' => 'required|max:255',
			'name' => 'required|max:255',
			'discount' => 'required|numeric|min:0|max:100',
			'enabled' => 'boolean',
			'cumulative' => 'boolean',
			'code' => 'required|max:255',
			'products' => 'array',
			'categories' => 'array',
			'single_use' => 'boolean',
			'roles' => 'array',
		];
    }
}
