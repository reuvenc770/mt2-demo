<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\FeedRepo;

class FeedApiRecordRequest extends Request
{
    protected $repo;

    public function __construct ( RawFeedEmailRepo $repo ) {
        parent::__construct();
        $this->repo = $repo;
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
        return [
            'email' => 'required|email' ,
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
        $this->repo->logFailure(
            $errors ,
            $this->fullUrl() ,
            json_encode( $this->ips() ) ,
            $this->input( 'email' ) ,
            FeedRepo::getFeedIdFromPassword( $this->input( 'pw' ) )
        );

        return new JsonResponse( $errors , 422 );
    }
}
