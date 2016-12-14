<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class AddProxyRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("proxy.add")){
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
            'name' => 'required|unique:proxies',
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
            'name.unique' => 'This proxy name already exists.',
            'provider_name.required' => 'The provider\'s name is required.' ,
            'cake_affiliate_id.required' => 'CAKE Affiliate is required.'
        ];
    }
}
