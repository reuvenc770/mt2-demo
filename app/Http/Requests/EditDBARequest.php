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
            'name' => 'required',
            'phone'         => 'required',
            'zip'           => 'required',
            'email'         => 'required|email',
            'address'       => 'required',
            'city'          => 'required',
            'state'         => 'required',
            'registrant_name' => 'required',
        ];
    }
}
