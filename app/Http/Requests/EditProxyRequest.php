<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class EditProxyRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("proxy.edit")){
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
            'ip_addresses'      => 'required',
            'provider_name'      => 'required',
            'cake_affiliate_id'      => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'ip_addresses.required' => 'At least 1 IP address is required.',
            'name.required' => 'Proxy name is required.',
            'provider_name.required' => 'The provider\'s name is required.' ,
            'cake_affiliate_id.required' => 'CAKE Affiliate is required.'
        ];
    }
}
