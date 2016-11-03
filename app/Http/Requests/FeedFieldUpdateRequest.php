<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class FeedFieldUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( 'api.feed.file.savefieldorder' ) ) {
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
            'email_index' => 'required' ,
            'source_url_index' => 'required' ,
            'capture_date_index' => 'required' ,
            'ip_index' => 'required'
        ];
    }

    public function messages () {
        return [
            'email_index.required' => 'Email must be included.' ,
            'source_url_index.required' => 'Source URL must be included.' ,
            'capture_date_index.required' => 'Capture Date must be included.' ,
            'ip_index.required' => 'IP must be included.'
        ];
    }
}
