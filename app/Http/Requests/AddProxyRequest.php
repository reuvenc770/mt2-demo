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
            'ip_address'      => 'required|ip',
            'provider_name'      => 'required',
        ];
    }
}