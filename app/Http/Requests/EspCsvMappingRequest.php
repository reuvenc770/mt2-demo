<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class EspCsvMappingRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess( 'api.esp.mappings.update' ) ) {
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
                'mappings' => 'array',
                'mappings.*' => 'in_array:campaign_name,datetime,name'

        ];
    }

    public function messages () {
        return [];

    }
}
