<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EspApiEditRequest extends Request
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
            'accountName' => 'required' ,
            'key1' => 'required|unique:esp_accounts,key_1,' . $this->input('id'),
            'customId' => 'integer|min:100000|unique:esp_accounts,custom_id,' . $this->input('id')
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'accountName.required' => 'ESP Account Name is required.' ,
            'key1.required' => 'ESP Key 1 is required.',
            'key1.unique' => 'This key is used by another ESP API account. Please enter a different key1.',
            'customId.integer' => 'Custom ID must be digits only. Do not include letters or special characters.',
            'customId.min' => 'Custom ID must be a minimum of 6 digits.',
            'customId.unique' => 'This custom ID is used by another ESP API account. Please enter a different custom ID.'
        ];
    }
}
