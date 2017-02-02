<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EspApiAddRequest extends Request
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
            'key1' => 'required|unique:esp_accounts,key_1',
            'customId' => 'integer|min:100000|max:4294967295|unique:esp_accounts,custom_id'
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
            'key1.unique' => 'ESP Key 1 already exists.',
            'customId.integer' => 'Custom ID must be digits only. Do not include letters or special characters.',
            'customId.min' => 'Custom ID must be a minimum of 6 digits.',
            'customId.max' => 'Custom ID cannot be larger than 4294967295.',
            'customId.unique' => 'This custom ID is used by another ESP API account. Please enter a different custom ID.'
        ];
    }
}
