<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class AddRegistrarRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("registrar.add")){
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
            'name'          => 'required',
            'username'      => 'required',
            'password'      => 'required',
            'last_cc'       => 'required',
            'contact_credit_card' => 'required',
            'dba_names'     => 'required'
        ];
    }

    public function messages ()
    {
        return [
            'name.required'         => 'Registrar name is required.',
            'username.required'     => 'Username is required.',
            'password.required'     => 'Password is required.',
            'last_cc.required'      => 'Last 4 digits of credit card is required.',
            'contact_credit_card.required' => 'The contact of the credit card is required.',
            'dba_names.required'    => 'At least 1 DBA is required.'
        ];
    }
}
