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
            'name' => 'required|unique:clients,name,'.$this->input('id') ,
            'email_address' => 'required' ,
            'status' => 'required'
        ];
    }

    public function messages ()
    {
        return [
            'name.required'         => 'Client name is required.',
            'name.unique'   => 'This client name already exists.',
            'email_address.required'     => 'An email address is required.',
            'status.required'     => 'A status is required.'
        ];
    }
}
