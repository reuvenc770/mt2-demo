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
        if(Sentinel::hasAccess("ispgroup.add")){
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
}
