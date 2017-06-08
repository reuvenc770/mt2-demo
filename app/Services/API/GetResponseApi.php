<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/8/16
 * Time: 3:44 PM
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;
use App\Facades\Guzzle;

class GetResponseApi extends EspBaseAPI
{
    const API_URL = "https://api.getresponse.com/v3/";
    const ESP_NAME = "GetResponse";

    protected $apiKey;
    protected $query = array();
    protected $action;

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);

        $this->apiKey = EspApiAccount::grabApiKey($espAccountId);
        $this->query = array("query");
    }

    public function sendApiRequest()
    {
        $response = Guzzle::get( $this->action , [
            'http_errors' => false ,
            'base_uri' => self::API_URL ,
            'query' => $this->query ,
            'headers' => $this->getDefaultHeaders()
        ] );

        return $this->parseResponse( $response );
    }

    public function sendDirectApiRequest($query){
        $response = Guzzle::get( $query , [
            'http_errors' => false ,
            'base_uri' => self::API_URL ,
            'headers' => $this->getDefaultHeaders()
        ] );

        return $this->parseResponse( $response );
    }

    public function contactExists ( $email ) {
        $response = Guzzle::get( self::API_URL . 'contacts' , [
            'http_errors' => false ,
            'query' => [ 'query[email]=' => $email , 'page=' => 1 , 'perPage=' => 1 ] ,
            'headers' => $this->getDefaultHeaders()
        ] );

        $exists = false;

        try {
            $exists = count( $this->parseResponse( $response ) ) === 1;
        } catch ( \Exception $e ) {
            #Contact does not exist.
        }

        return $exists;
    }

    public function addContact ( $campaignId , $pii ) {
        $this->validAddContactRequestCheck( $campaignId , $pii );

        $response = Guzzle::post( self::API_URL . 'contacts' , [
            'http_errors' => false ,
            'json' => $this->buildAddContactJsonRequest( $campaignId , $pii ) ,
            'headers' => $this->getDefaultHeaders()
        ] );

        return $this->parseResponse( $response );
    }

    protected function validAddContactRequestCheck ( $campaignId , $pii ) {
        if ( !isset( $campaignId ) || empty( $campaignId ) ) {
            throw new \Exception( 'Campaign ID is required for adding contacts to GetResponse campaigns.' );
        }

        if ( !isset( $pii[ 'id' ] ) || empty( $pii[ 'id' ] ) ) {
            throw new \Exception( 'Email ID [id] is required for adding contacts to GetResponse campaigns.' );
        }

        if ( !isset( $pii[ 'email' ] ) || empty( $pii[ 'email' ] ) ) {
            throw new \Exception( 'Email [email] is required for adding contacts to GetResponse campaigns.' );
        }

        if ( !isset( $pii[ 'firstName' ] ) || empty( $pii[ 'firstName' ] ) ) {
            throw new \Exception( 'First Name [firstName] is required for adding contacts to GetResponse campaigns.' );
        }

        if ( !isset( $pii[ 'lastName' ] ) || empty( $pii[ 'lastName' ] ) ) {
            throw new \Exception( 'Last Name [lastName] is required for adding contacts to GetResponse campaigns.' );
        }
    }

    protected function buildAddContactJsonRequest ( $campaignId , $pii ) {
        $jsonRequest = [
            'name' => "{$pii[ 'firstName' ]} {$pii[ 'lastName' ]}",
            'email' => $pii[ 'email' ] ,
            'campaign' => [ 'campaignId' => $campaignId ] ,
            'customFieldValues' => [
                [ 'customFieldId' => 'FPVCC' , 'value' => [ $pii[ 'id' ] ] ]
            ]
        ]; 

        return $jsonRequest;
    }

    public function setAction($action){
        $this->action = $action;

        return $this;
    }

    public function setQuery($query){
        $this->query = $query;

        return $this;
    }

    protected function getDefaultHeaders () {
        return [
            'Content-type' => 'application/json' ,
            'X-Auth-Token' => "api-key {$this->apiKey}"
        ];
    }

    protected function parseResponse ( $response ) {
        $data = $response->getBody()->getContents();

        return json_decode( $data , true );
    }
}
