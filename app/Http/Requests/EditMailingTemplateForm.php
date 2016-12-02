<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class EditMailingTemplateForm extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("mailingtemplate.edit")){
            return true;
        }
        return false;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'templateType'      => 'required',
            'html'      => 'required',
            'selectedEsps'      => 'required',
        ];
    }

    public function messages ()
    {
        return [
            'name.required'         => 'Template name is required.',
            'templateType.required' => 'Template type is required.',
            'html.required'         => 'HTML is required.',
            'selectedEsps.required' => 'At least 1 ESP account is required.'
        ];
    }
}
