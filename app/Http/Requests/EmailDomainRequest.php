<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class EmailDomainRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("isp.add")){
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
            'domain_name' => 'required',
            'domain_group_id' => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'domain_name.required'         => 'Domain name is required.',
            'domain_group_id.required'     => 'A domain group is required.'
        ];
    }
}
