<?php

namespace App\Services\API;

use App\Facades\Guzzle;
use Maknz\Slack\Facades\Slack;

class EmailOversightApi {
    CONST API_URL = 'https://api.emailoversight.com/api/emailvalidation';

    protected $token;
    protected $response;
    protected $requestBody;
    protected $requestHeaders;
    protected $slackChannel = '#cmp_hard_start_errors';

    public function __construct () {
        $this->loadTokenFromConfig();
    }

    public function setToken ( $token ) {
        $this->token = $token;
    }

    public function verifyEmail ( $listId , $email ) {
        try {
            $this->requestBody = json_encode( [
                "ListId" => $listId , 
                "Email" => $email
            ] );

            $this->requestHeaders = [
                "ApiToken" => $this->token ,
                "Content-Type" => "application/json" ,
            ];

            $this->response = Guzzle::post( self::API_URL , [
                "body" => $this->requestBody ,
                "headers" => $this->requestHeaders ,
                "verify" => false ,
                "http_errors" => true ,
            ] );

            return json_decode( $this->response->getBody()->getContents() );
        } catch ( \Exception $e ) {
            \Log::error( $e );

            Slack::to( $this->slackChannel )->send(
                "Email Oversight - API Error:\n" 
                . "RequestHeaders: " . json_encode( $this->requestHeaders ) 
                . "\nRequestBody: {$this->requestBody}\n"
                . $e->getMessage()
            );

            throw $e;
        }
    }

    public function getLastRequestHeaders () {
        return $this->requestHeaders;
    }

    public function getLastRequestBody () {
        return $this->requestBody;
    }

    public function getLastResponse () {
        return $this->response;
    }

    protected function loadTokenFromConfig () {
        $this->setToken( config( 'services.emailoversight.token' ) );

        $this->checkToken();
    }

    protected function checkToken () {
        if ( is_null( $this->token ) ) {
            throw new \Exception( 'Email Oversight - Missing Token.' );
        }
    }
}
