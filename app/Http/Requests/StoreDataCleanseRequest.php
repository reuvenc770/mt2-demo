<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreDataCleanseRequest extends Request
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
            'pname' => 'required' ,
            'ConfirmEmail' => 'required' ,
            'aid' => 'required'
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'pname.required' => 'Data export filename is required.' ,
            'ConfirmEmail.required' => 'Confirmation email is required.',
            'aid.required' => 'At least 1 advertiser is required.'
        ];
    }
}
