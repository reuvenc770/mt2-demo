<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/25/16
 * Time: 4:58 PM
 */

namespace App\Http\Validators;

use \Illuminate\Validation\Validator;
use Hash;

class HashValidator extends Validator {

    public function validateHash($attribute, $value, $parameters) {
        return Hash::check($value, $parameters[0]);
    }
}