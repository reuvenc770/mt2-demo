<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SeedEmailRequest extends Request
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
            'email_address' => 'required|email'
        ];
    }

    public function messages ()
    {
        return [
            'email_address.required' => 'An email address is required.',
            'email_address.email' => 'Email address is not in a valid format.'
        ];
    }
}
