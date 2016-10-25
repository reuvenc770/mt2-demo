<?php

namespace App\Http\Requests;
use Sentinel;
use App\Http\Requests\Request;
use Log;

class FeedEditRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Sentinel::hasAccess("feed.edit")) {
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
            'client_id' => 'required' ,
            'party' => 'required' ,
            'short_name' => 'required' ,
            'status' => 'required' ,
            'vertical_id' => 'required' ,
            'frequency' => 'required' ,
            'type_id' => 'required' ,
            'country_id' => 'required|integer' ,
            'source_url' => 'required'
        ];
    }

    public function message() {
        return [
            'client_id.required' => 'Client is required.' ,
            'party.required' => 'Party is required.' ,
            'short_name.required' => 'Short name is required.' ,
            'status.required' => 'Status is required.' ,
            'vertical_id.required' => 'Feed vertical is required.' ,
            'frequency.required' => 'Frequency is required.' ,
            'type_id.required' => 'Feed type is required.' ,
            'country_id.required' => 'Country is required.' ,
            'source_url.required' => 'Source URL is required.'
        ];
    }
}
