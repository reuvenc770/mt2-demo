<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class FeedGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( "feedgroup.edit" ) || Sentinel::hasAccess( "feedgroup.add" ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required' ,
        ];
    }
    /**
     *
     */
    public function messages ()
    {
        return [
            'name.required' => 'A feed group name is required.' ,
        ];
    }
}
