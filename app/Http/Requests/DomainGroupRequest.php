<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class DomainGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if( $this->getMethod() === 'POST' && Sentinel::hasAccess("ispgroup.add") ){
            return true;
        } else if ( $this->getMethod() === 'PUT' && Sentinel::hasAccess('ispgroup.edit') ) {
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
            "name" => 'required',
            "country" => 'required'
        ];
    }

    public function messages ()
    {
        return [
            'name.required'         => 'An ISP group name is required.',
            'country.required'     => 'The ISP group must be assigned a country.'
        ];
    }
}
