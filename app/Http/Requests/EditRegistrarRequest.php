<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class EditRegistrarRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("registrar.edit")){
            return true;
        }
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
            'name' => 'required',
            'username'      => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'name.required'         => 'Registrar name is required.',
            'username.required'     => 'Username is required.'
        ];
    }
}
