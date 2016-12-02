<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EspEditRequest extends Request
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
            'email_id_field' => 'required'
        ];
    }

    public function messages ()
    {
        return [
            'email_id_field.required' => 'Email ID field is required.'
        ];
    }
}
