<?php

namespace App\Http\Requests;
use Sentinel;
use App\Http\Requests\Request;
use Log;

class ClientEditRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Sentinel::hasAccess("client.edit")) {
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
            'client_main_name' => 'required',
            'email_addr' => 'required|email',
            'username' => 'required',
            'password' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required|regex:/^\d{5}$/',
            'state' => 'required|regex:/^[a-zA-Z]{2}$/',
            'phone' => 'required|alpha_dash',
            'network' => 'required',
            'client_type' => 'required',
            'list_owner' => 'required|integer',
            'country_id' => 'required|integer',
            'check_previous_oc' => 'required',
            'client_has_client_group_restrictions' => 'required',
            'check_global_suppression' => 'required',
            'status' => 'required',
        ];
    }

    protected function getValidatorInstance() {
        $this->updateUrls();
        return parent::getValidatorInstance();
    }

    protected function updateUrls() {
        $data = $this->all();

        if ('http://' !== substr($data['client_record_source_url'], 0, 7)) {
            $newUrl = 'http://' . $data['client_record_source_url'];
            $this->merge(array('client_record_source_url' => $newUrl));
        }
        if ('http://' !== substr($data['ftp_url'], 0, 7)) {
            $newUrl = 'http://' . $data['ftp_url'];
            $this->merge(array('ftp_url' => $newUrl));
        }
    }

    public function message() {
        return [
            'permissions.required' => "You do not have the privileges to do that.",
            'client_main_name.required' => 'Main contact name is required.',
            'email_addr.required' => 'Contact email address is required.',
            'email_addr.email' => 'Contact email address must be an email address.',
            'username.required' => 'Client name is required.',
            'password.required' => 'Password is required.',
            'address.required' => 'Client street address is required.',
            'city.required' => 'Client city is required.',
            'zip.required' => 'Client zip code is required.',
            'zip.regex' => 'Client zip code must be formatted correctly.',
            'state.required' => 'Client state is required.',
            'state.regex' => 'Client state must be formatted correctly (e.g. "NY")',
            'phone.required' => 'Client phone number is required.',
            'phone.alpha_dash' => 'Client phone must be formatted correctly. Either do not separate the numbers or separate them with dashes.',
            'network.required' => 'Client network is required.',
            'client_type.required' => 'Client type is required.',
            'list_owner.required' => 'List owner is required.',
            'country_id.required' => 'Country ID is required.',
            'country_id.integer' => 'Country ID must be a number',
            'check_previous_oc.required' => 'Check previous OC is required.',
            'client_has_client_group_restrictions.required' => 'Client has client group restrictions is required.',
            'check_global_suppression.required' => 'Answer to "check glocal suppression" is required.',
            'status.required' => 'Status is required.',
        ];
    }
}
