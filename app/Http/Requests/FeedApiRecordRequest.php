<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\FeedRepo;

class FeedApiRecordRequest extends Request
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
    public function rules()
    {
        return [
            'email_address' => 'required|email' ,
            'ip' => 'required|ip' ,
            'capture_date' => 'required|date' ,
            'source_url' => 'required' ,
            'pw' => 'required|exists:feeds,password'
        ];
    }

    public function messages () {
        return [
            'pw.exists' => 'Your password does not exist. Please fix or reach out to your representative.'
        ];
    }

    public function response ( array $errors ) {
        RawFeedEmailRepo::logFailure(
            $errors ,
            $this->fullUrl() ,
            $this->ip() ,
            FeedRepo::getFeedIdFromPassword( $this->input( 'pw' ) )
        );

        return new JsonResponse( $errors , 422 );
    }
}
