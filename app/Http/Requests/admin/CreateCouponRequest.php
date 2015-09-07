<?php

namespace Friluft\Http\Requests\admin;

use Friluft\Http\Requests\Request;

class CreateCouponRequest extends Request
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
			'code' => 'required|unique:coupons|max:255',
			'enabled' => 'boolean',
			'cumulative' => 'boolean',
			'products' => 'array',
			'categories' => 'array',
			'single_use' => 'boolean',
			'roles' => 'array',
        ];
    }
}
