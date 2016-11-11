<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class ClientUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( 'api.client.update' ) ) {
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
            'name' => 'required' ,
            'email_address' => 'required' ,
            'status' => 'required'
        ];
    }
}
