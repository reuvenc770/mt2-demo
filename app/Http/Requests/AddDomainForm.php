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
        if ( $this->input('domain_type') == 2 ) {
            $isProxyRequired = 'required';
        } else {
            $isProxyRequired = '';
        }

        return [
            'espName'        => 'required',
            'espAccountId'   => 'required',
            'registrar'      => 'required',
            'dba'            => 'required',
            'domains'        => 'required',
            'proxy'          => $isProxyRequired,
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
            'proxy.required'        => 'A proxy is required for content domain.',
            'domains.required'      => 'All domain information is required and must be in the correct format.',
            'live_a_record.required'=> 'Is the A-Record live?'
        ];
    }
}
