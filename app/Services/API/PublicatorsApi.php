<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;

use Carbon\Carbon;
use App\Facades\Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;

class PublicatorsApi extends EspBaseAPI {
    const ESP_NAME = "Publicators";

    const API_BASE_URL = "https://apiv1.publicators.com";
    const API_AUTH = "/api/Authorization/SetCustomerAuthorization";
    const API_LIST_CAMPAIGNS = "/api/Campaigns/GetCampaignsBasicDetailsByDatesRange";
    const API_CAMPAIGNS_STATS = "/api/Reports/GetCampaignStatisticByCampaignId";
    const API_SENT_STATS = "/api/Reports/GetReportAllSentMailsByCampaignID";
    const API_OPENS_STATS = "/api/Reports/GetReportAllOpenedMailsByCampaignID";
    const API_CLICKS_STATS = "/api/Reports/GetReportAllClickedMailsByCampaignID";
    const API_BOUNCES_STATS = "/api/Reports/GetReportAllBouncedMailsByCampaignID";

    const DATE_FORMAT = "Y/m/d H:i";

    protected $username;
    protected $password;
    protected $token;

    protected $date;
    protected $currentCampaignId;

    protected $callType;
    protected $typeList = [
        "auth" => "AUTH" ,
        "listCampaigns" => "LIST_CAMPAIGNS" ,
        "campaignsStats" => "CAMPAIGNS_STATS" ,
        "sentStats" => "SENT_STATS" ,
        "opensStats" => "OPENS_STATS",
        "clicksStats" => "CLICKS_STATS",
        "bouncesStats" => "BOUNCES_STATS"
    ];

    protected $defaultRequestOptions = [
        "verify" => false ,
        "headers" => [ "Content-Type" => "application/json" ]
    ];

    public function __construct ( $espAccountId ) {
        $this->espAccountId = $espAccountId;

        parent::__construct( self::ESP_NAME , $espAccountId );

        $this->loadCreds();
    }

    public function setDate ( $date ) {
        $this->date = $date;
    }

    public function isAuthenticated () {
        return !is_null( $this->token );
    }

    public function authenticate () {
        if ( empty( $this->username ) || empty( $this->password ) ) {
            throw new \Exception( "Missing credentials. Can not authenticate into Publicators API." );
        }

        $this->setCallType( "auth" );

        $response = $this->sendApiRequest();

        $responseBody = json_decode( $response->getBody() );

        if ( is_null( $responseBody ) ) {
            throw new \Exception( "Failed to parse authentication response. '{$responseBody}'" );
        }

        $this->token = $responseBody->Token;
    }

    public function getCampaigns () {
        $this->setCalltype( "listCampaigns" );

        $response = $this->sendApiRequest();

        $responseBody = json_decode( $response->getBody() );

        if ( is_null( $responseBody ) ) {
            throw new \Exception( "Failed to parse campaign listing response. '{$responseBody}'" );
        }

        return $responseBody;
    }

    public function getCampaignStats ( $campaignId ) {
        if ( empty( $campaignId ) ) {
            throw new \Exception( "Campaign ID is required for this call." );
        }

        $this->setCallType( "campaignsStats" );
        $this->setCampaignId( $campaignId );

        $response = $this->sendApiRequest();

        $responseBody = json_decode( $response->getBody() );

        if ( is_null( $responseBody ) ) {
            throw new \Exception( "Failed to parse campaign stats response. '{$responseBody}'" );
        }

        return $responseBody;
    }

    public function sendApiRequest () {
        if ( is_null( $this->callType ) ) {
            throw new \Exception( "Call type not defined." );
        }

        $url = $this->constructUrl();
        $options = $this->constructOptions();

        try {
            $response = Guzzle::post( $url , $options );
        } catch ( ClientException $e ) {
            $this->outputFailedDebug( $e );

            throw new \Exception( "Client Error Detected. " . $e->getMessage() , 400 , $e );
        } catch ( ServerException $e ) {
            $this->outputFailedDebug( $e );

            throw new \Exception( "Client Server Error Detected. " . $e->getMessage() , 500 , $e );
        } catch ( RequestException $e ) {
            $this->outputFailedDebug( $e );

            throw new \Exception( "Network Error Detected. " . $e->getMessage() , 100 , $e );
        }

        return $response;
    }

    public function setCallType ( $type ) {
        if ( !in_array( $type , array_keys( $this->typeList ) ) ) {
            throw new \Exception( "{$type} is not a valid API call type." );
        }

        $this->callType = $type;
    }

    protected function loadCreds () {
        $creds = EspApiAccount::grabApiUsernameWithPassword( $this->espAccountId );

        $this->username = $creds[ "userName" ];
        $this->password = $creds[ "password" ];
    }

    protected function setCampaignId ( $campaignId ) {
        $this->currentCampaignId = $campaignId;
    }

    protected function constructUrl () {
        return self::API_BASE_URL . constant( "App\Services\API\PublicatorsApi::API_" . $this->typeList[ $this->callType ] );
    }

    protected function constructOptions () {
        switch ( $this->callType ) {
            case "auth" :
                return $this->defaultRequestOptions + [ "body" => json_encode( [ "Username" => $this->username , "Password" => $this->password ] ) ];
            break;

            case "listCampaigns" :
                return $this->defaultRequestOptions + [
                    "body" => json_encode( [
                        "Auth" => [ "Token" => $this->token ] ,
                        "FromSentDate" => Carbon::parse( $this->date )->startOfDay()->format( self::DATE_FORMAT ) ,
                        "ToSentDate" => Carbon::parse( $this->date )->endOfDay()->format( self::DATE_FORMAT )
                    ] )
                ];
            break;

            case "campaignsStats" :
                return $this->defaultRequestOptions + [
                    "body" => json_encode( [
                        "Auth" => [ "Token" => $this->token ] ,
                        "ID" => $this->currentCampaignId
                    ] )
                ];
            break;
        }
    }

    protected function outputFailedDebug ( $exception ) {
        $request = $exception->getRequest();

        echo "\tRequest Headers:\n";
        foreach ( $request->getHeaders() as $name => $values ) {
            echo "\t" . $name . ": " . implode( ", " , $values ) . "\n";
        }

        echo "\tRequest Body:\n";
        echo "\t" . $request->getBody();

        if( $response = $exception->getResponse() ) {
            echo "\n\n\tResponse:\n";
            echo "\t" . $response->getBody() . "\n";
        }
    }
}
