<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\FeedRepo;

class FeedApiRecordRequest extends Request
{
    const US_COUNTRY_ID = 1;

    protected $repo;
    protected $feedRepo;

    public function __construct ( RawFeedEmailRepo $repo , FeedRepo $feedRepo ) {
        parent::__construct();
        $this->repo = $repo;
        $this->feedRepo = $feedRepo;
    }
    
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
        $rules = [
            'email' => 'required|email' ,
            'ip' => 'required|ip' ,
            'capture_date' => 'required|date' ,
            'source_url' => 'required' ,
            'pw' => 'required|exists:feeds,password'
        ];

        $feedId = FeedRepo::getFeedIdFromPassword( $this->input( 'pw' ) );
        $isEuroDateFormat = ( $this->feedRepo->getFeedCountry( $feedId ) !== self::US_COUNTRY_ID );

        if ( $isEuroDateFormat ) {
            $rules[ 'capture_date' ] = 'required|date_format:d/m/Y';
        }

        return $rules;
    }

    public function messages () {
        return [
            'pw.exists' => 'Your password does not exist. Please fix or reach out to your representative.'
        ];
    }

    public function response ( array $errors ) {
        $this->repo->logFailure(
            $errors ,
            $this->fullUrl() ,
            json_encode( $this->ips() ) ,
            ( $this->input( 'email' ) != '' ? $this->input( 'email' ) : '' ) ,
            FeedRepo::getFeedIdFromPassword( $this->input( 'pw' ) )
        );

        return new JsonResponse( [ "status" => false , "messages" => $errors ] , 422 );
    }
}
