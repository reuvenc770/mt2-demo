<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EspAddRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'email_id_field' => 'required'
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'name.required' => 'ESP account name is required.' ,
            'email_id_field.required' => 'Email ID field is required.'
        ];
    }
}
