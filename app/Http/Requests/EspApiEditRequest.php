<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Services\EspApiAccountService;

class EspApiEditRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(EspApiAccountService $espAccountService)
    {
        $this->espAccountService = $espAccountService;
        $currentEspAccount = $this->espAccountService->getAccount( $this->input('id') );
        $currentCustomId = $currentEspAccount['custom_id'];

        if ( $currentCustomId != null ){
            $isCustomIdRequired = 'required|';
        } else {
            $isCustomIdRequired = '';
        }

        return [
            'accountName' => 'required' ,
            'key1' => 'required|unique:esp_accounts,key_1,' . $this->input('id'),
            'customId' => $isCustomIdRequired . 'integer|min:100000|max:4294967295|unique:esp_accounts,custom_id,' . $this->input('id'),
        ];
    }

    /**
     *
     */
    public function messages ()
    {
        return [
            'accountName.required' => 'ESP Account Name is required.' ,
            'key1.required' => 'ESP Key 1 is required.',
            'key1.unique' => 'This key is used by another ESP API account. Please enter a different key1.',
            'customId.integer' => 'Custom ID must be digits only. Do not include letters or special characters.',
            'customId.min' => 'Custom ID must be a minimum of 6 digits.',
            'customId.max' => 'Custom ID cannot be larger than 4294967295.',
            'customId.unique' => 'This custom ID is used by another ESP API account. Please enter a different custom ID.',
            'customId.required' => 'Since a custom ID was set for this ESP API account, it is now required.'
        ];
    }
}
