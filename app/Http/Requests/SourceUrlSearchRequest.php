<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class SourceUrlSearchRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( "api.feed.searchsource" ) ) {
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
            'startDate' => 'required|date' ,
            'endDate' => 'required|date' ,
        ];
    }
}
