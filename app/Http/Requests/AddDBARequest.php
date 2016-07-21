<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class AddDBARequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("dba.add")){
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
            'name' => 'required|unique:doing_business_as|',
            'state_id'      => 'required',
        ];
    }
}
