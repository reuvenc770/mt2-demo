<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class EditDBARequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("dba.edit")){
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
            'dba_name' => 'required',
            'phone'         => 'required',
            'zip'           => 'required',
            'dba_email'         => 'email',
            'address'       => 'required',
            'city'          => 'required',
            'state'         => 'required'
        ];
    }

    public function messages ()
    {
        return [
            'dba_name.required' => 'DBA name is required.' ,
            'phone.required'    => 'Phone number is required.' ,
            'zip.required'      => 'Zip code is required.' ,
            'dba_email.email'   => 'Email is not in a valid format.' ,
            'address.required'  => 'An address is required.' ,
            'city.required'     => 'City is required.' ,
            'state.required'    => 'State is required.'
        ];
    }
}
