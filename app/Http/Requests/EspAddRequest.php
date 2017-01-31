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
            'name'      => 'required|unique:esps',
            'email_id_field' => 'required',
            'nickname' => 'required|unique:esps'
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'name.required' => 'ESP account name is required.' ,
            'name.unique' => 'An ESP account with this name already exists.',
            'email_id_field.required' => 'Email ID field is required.',
            'nickname.required' => 'A nickname for the ESP is required.',
            'nickname.unique' => 'This nickname is used by another ESP account. Please enter a different nickname.'
        ];
    }
}
