<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\RawFeedEmailRepo;
use App\Services\FeedService;

class FeedApiRecordRequest extends Request
{
    const US_COUNTRY_ID = 1;

    protected $repo;
    protected $feedService;

    public function __construct ( RawFeedEmailRepo $repo , FeedService $feedService ) {
        parent::__construct();
        $this->repo = $repo;
        $this->feedService = $feedService;
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
        return $this->feedService->generateValidationRules( $this->all() );
    }

    public function messages () {
        return [
            'pw.exists' => 'Your password does not exist. Please fix or reach out to your representative.'
        ];
    }

    public function response ( array $errors ) {
        $log = $this->repo->logRealtimeFailure(
            $errors ,
            $this->fullUrl() ,
            json_encode( $this->ips() ) ,
            ( $this->input( 'email' ) != '' ? $this->input( 'email' ) : '' ) ,
            $this->feedService->getFeedIdFromPassword( $this->input( 'pw' ) )
        );

        foreach ( $errors as $field => $errorList ) {
            foreach ( $errorList as $currentError ) {
                if ( preg_match( "/required/" , $currentError ) === 0 ) {
                    $this->repo->logFieldFailure(
                        $field ,
                        $this->input( $field ) ,
                        $errorList ,
                        $log->id
                    );

                    break;
                }
            }
        }

        return new JsonResponse( [ "status" => false , "messages" => $errors ] , 422 );
    }
}
