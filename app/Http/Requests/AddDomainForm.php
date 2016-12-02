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
            'live_a_record'  => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'espName.required'      => 'An ESP is required.',
            'espAccountId.required' => 'An ESP account is required.',
            'registrar.required'    => 'A registrar is required.',
            'dba.required'          => 'A DBA is required.',
            'domains.required'      => 'Domain information is required and must be in the correct format.',
            'live_a_record.required'=> 'Is the A-Record live?'
        ];
    }
}
