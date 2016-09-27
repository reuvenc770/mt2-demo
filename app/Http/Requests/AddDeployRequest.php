<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Sentinel;
class AddDeployRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Sentinel::hasAccess("api.deploy.store")){
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
            'esp_account_id'        => 'required',
            #'list_profile_id'   => 'required',
            'template_id'      => 'required',
            'offer_id'            => 'required',
            'creative_id'            => 'required',
            'from_id'            => 'required',
            'subject_id'            => 'required',
            'content_domain_id'            => 'required',
            'mailing_domain_id'        => 'required',
            'cake_affiliate_id'          => 'required',
            'encrypt_cake'              => 'required',
            'fully_encrypt'          => 'required',
            'url_format'            => 'required'
        ];
    }
}
