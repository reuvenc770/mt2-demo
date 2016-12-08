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
            'list_profile_combine_id'   => 'required_if:party,3',
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

    public function messages ()
    {
        return [
            'esp_account_id.required'   => 'An ESP account is required.',
            'template_id.required'      => 'A template is required.',
            'offer_id.required'         => 'An offer name is required.',
            'creative_id.required'      => 'A creative is required.',
            'from_id.required'          => 'The from field is required.',
            'subject_id.required'       => 'The subject field is required.',
            'content_domain_id.required'=> 'Content domain is required.',
            'mailing_domain_id.required'=> 'Mailing domain is required.',
            'cake_affiliate_id.required'=> 'Cake ID is required.',
            'encrypt_cake.required'     => 'Encrypt cake?',
            'fully_encrypt.required'    => 'Fully encrypt?',
            'url_format.required'       => 'What is the URL format?'
        ];
    }
}
