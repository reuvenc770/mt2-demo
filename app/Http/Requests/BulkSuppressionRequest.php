<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BulkSuppressionRequest extends Request
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
            'suppressionReasonCode' => 'required'
        ];
    }

    public function messages ()
    {
        return [
            'suppressionReasonCode.required' => 'A suppression reason is required.'
        ];
    }
}
