<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class SubmitListProfileRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( 'listprofile.add' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     
     * @return array
     */
    public function rules()
    {
        return [
            'actionRanges' => 'required_without_all:actionRanges.deliverable,actionRanges.opener,actionRanges.clicker,actionRanges.converter' ,
            'selectedColumns' => 'required'
        ];
    }

    public function messages () {
        return [
            'actionRanges.required_without_all' => 'You must include at least one action range.' ,
            'selectedColumns.required' => 'You must select some columsn for export.'
        ];
    }
}
