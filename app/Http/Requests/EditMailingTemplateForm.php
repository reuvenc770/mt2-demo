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
        if(Sentinel::hasAccess("mailingtemplates.edit")){
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
}
