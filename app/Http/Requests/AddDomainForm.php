<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class AddDomainForm extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("domain.add")){
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
            'espName'        => 'required',
            'espAccountId'   => 'required',
            'registrar'      => 'required',
            'dba'            => 'required',
            'domains'        => 'required',
        ];
    }
}
