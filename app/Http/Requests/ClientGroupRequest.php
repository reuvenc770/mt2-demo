<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ClientGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( "clientgroup.edit" ) || Sentinel::hasAccess( "clientgroup.add" ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'gid' => 'required|regex:/^\d$/' ,
            'user_id' => 'required|regex:/^\d$/' ,
            'groupName' => 'required|' ,
            'excludeFromSuper' => 'required|regex:/[YN]{1}/'
        ];
    }
}
