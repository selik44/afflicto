<?php

namespace Friluft;

use Illuminate\Support\Facades\Validator;

/**
 * Created by PhpStorm.
 * User: darinx
 * Date: 02.03.17
 * Time: 15:24
 */



use Illuminate\Database\Eloquent\Model;


class ValidatorReviews extends Validator {

    // Validation rules for the ratings
    public function getCreateRules()
    {
        return array(
            'comment'=>'required|min:10',
            'rating'=>'required|integer|between:1,5'
        );
    }

}