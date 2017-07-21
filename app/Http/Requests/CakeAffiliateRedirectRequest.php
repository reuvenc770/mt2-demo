<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;

class CakeAffiliateRedirectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( Sentinel::hasAccess("api.affiliates.store") && Sentinel::hasAccess("api.affiliates.update") ) {
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
            'id' => 'required_without:new_affiliate_id' ,
            'offer_payout_type_id' => 'required' ,
            'redirect_domain' => 'required',
            'new_affiliate_id' => 'required_without:id',
            'new_affiliate_name' => 'required_without:id'
        ];
    }

    public function messages () {
        return [
            'id.required_without' => 'Affiliate is required.' ,
            'offer_payout_type_id.required' => 'Offer Payout Type is required.' ,
            'redirect_domain.required' => 'Redirect Domain is required.',
            'new_affiliate_id.required_without' => 'Affiliate is required.',
            'new_affiliate_name.required_without' => 'Affiliate is required.'
        ];
    }
}
