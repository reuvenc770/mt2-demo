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
            'contact_name'  => 'required',
            'contact_email' => 'required|email',
            'phone_number'  => 'required',
            'last_cc'       => 'required',
            'contact_credit_card' => 'required',
            'address'       => 'required',
            'city'          => 'required',
            'state'         => 'required',
            'zip'           => 'required|integer',
            'entity_name'   => 'required',

        ];
    }
}
