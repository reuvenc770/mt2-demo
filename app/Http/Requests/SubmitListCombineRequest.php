<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class SubmitListCombineRequest extends Request
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
            'combineName' => 'required' ,
            'ftpFolder' => 'required' ,
            'selectedProfiles' => 'required|min:2'
        ];
    }

    public function messages () {
        return [
            'combineName.required' => 'A combine name is required.' ,
            'ftpFolder.required' => 'FTP folder cannot be blank' ,
            'selectedProfiles.required' => 'A list profile is required.',
            'selectedProfiles.min' => 'A combine must include at least 2 list profiles.'
        ];
    }
}
