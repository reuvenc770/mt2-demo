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
            'espId' => 'required' ,
            'accountName' => 'required|unique:esp_accounts,account_name' ,
            'key1' => 'required|unique:esp_accounts,key_1'
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'espId.required' => 'Please choose an ESP.' ,
            'accountName.required' => 'ESP Account Name is required.' ,
            'accountName.unique' => 'ESP Account already exists.' ,
            'key1.required' => 'ESP Key 1 is required.' ,
            'key1.unique' => 'ESP Key 1 already exists.'
        ];
    }
}
