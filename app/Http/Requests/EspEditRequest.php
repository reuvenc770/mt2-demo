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
            'accountName' => 'required' ,
            'key1' => 'required'
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'accountName.required' => 'ESP Account Name is required.' ,
            'key1.required' => 'ESP Key 1 is required.'
        ];
    }
}
