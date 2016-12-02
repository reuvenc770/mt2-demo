<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class AddMailingTemplateForm extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("mailingtemplate.add")){
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
            'name' => 'required|unique:mailing_templates,template_name',
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
            'html.required'         => 'HTML is required.'
        ];
    }
}
