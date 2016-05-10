<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\API;

use Cache;
use Log;
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
    const API_UNSUBSCRIBED_STATS = "/api/Reports/GetReportAllUnsubscribedMailsByCampaignID";

    const TYPE_AUTH = 'auth';
    const TYPE_LIST_CAMPAIGNS = 'listCampaigns';
    const TYPE_CAMPAIGN_STATS = 'campaignsStats';
    const TYPE_SENT_STATS = 'sentStats';
    const TYPE_OPENS_STATS = 'opensStats';
    const TYPE_CLICKS_STATS = 'clicksStats';
    const TYPE_BOUNCES_STATS = 'bouncesStats';
    const TYPE_UNSUBSCRIBED_STATS = 'unsubscribedStats';

    const DATE_FORMAT = "Y/m/d H:i";

    const CACHE_TAG = "publicators";
    const CACHE_KEY = "API_TOKEN";
    const CACHE_TIMEOUT = 4; #in mins

    protected $username;
    protected $password;
    protected $token;

    protected $date;
    protected $currentCampaignId;

    protected $callType;
    protected $typeListMap = [
        PublicatorsApi::TYPE_AUTH => PublicatorsApi::API_AUTH ,
        PublicatorsApi::TYPE_LIST_CAMPAIGNS => PublicatorsApi::API_LIST_CAMPAIGNS ,
        PublicatorsApi::TYPE_CAMPAIGN_STATS => PublicatorsApi::API_CAMPAIGNS_STATS ,
        PublicatorsApi::TYPE_SENT_STATS => PublicatorsApi::API_SENT_STATS ,
        PublicatorsApi::TYPE_OPENS_STATS => PublicatorsApi::API_OPENS_STATS ,
        PublicatorsApi::TYPE_CLICKS_STATS => PublicatorsApi::API_CLICKS_STATS ,
        PublicatorsApi::TYPE_BOUNCES_STATS => PublicatorsApi::API_BOUNCES_STATS ,
        PublicatorsApi::TYPE_UNSUBSCRIBED_STATS => PublicatorsApi::API_UNSUBSCRIBED_STATS
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

        if ( $this->cachedTokenAvailable() ) {
            $this->token = $this->getCachedToken();
        } else {
            $this->setCallType( self::TYPE_AUTH );

            $response = $this->sendApiRequest();

            $responseBody = json_decode( $response->getBody() );

            if ( is_null( $responseBody ) ) {
                throw new \Exception( "Failed to parse authentication response. '{$responseBody}'" );
            }

            $this->cacheNewToken( $responseBody->Token );
            $this->token = $responseBody->Token;
        }
    }

    public function getCampaigns () {
        $this->setCalltype( self::TYPE_LIST_CAMPAIGNS );

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

        $this->setCallType( self::TYPE_CAMPAIGN_STATS );
        $this->setCampaignId( $campaignId );

        $response = $this->sendApiRequest();

        $responseBody = json_decode( $response->getBody() );

        if ( is_null( $responseBody ) ) {
            throw new \Exception( "Failed to parse campaign stats response. '{$responseBody}'" );
        }

        return $responseBody;
    }

    public function getRecordStats ( $recordType , $campaignId ) {
        $this->setCallType( $recordType );
        $this->setCampaignId( $campaignId );

        $response = $this->sendApiRequest();

        $responseBody = json_decode( $response->getBody() );

        if ( is_null( $responseBody ) ) {
            throw new \Exception( "Failed to parse campaign '{$recordType}' stats response. '{$responseBody}'" );
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
            $output = $this->outputFailedDebug( $e );

            throw new \Exception( "Client Error Detected.\n" . $output . $e->getMessage() , 400 , $e );
        } catch ( ServerException $e ) {
            $output = $this->outputFailedDebug( $e );

            throw new \Exception( "Client Server Error Detected.\n" . $output . $e->getMessage() , 500 , $e );
        } catch ( RequestException $e ) {
            $output = $this->outputFailedDebug( $e );

            throw new \Exception( "Network Error Detected.\n" . $output . $e->getMessage() , 100 , $e );
        }

        return $response;
    }

    public function setCallType ( $type ) {
        if ( !in_array( $type , array_keys( $this->typeListMap ) ) ) {
            throw new \Exception( "{$type} is not a valid API call type." );
        }

        $this->callType = $type;
    }

    protected function loadCreds () {
        $creds = EspApiAccount::grabApiUsernameWithPassword( $this->espAccountId );

        $this->username = $creds[ "userName" ];
        $this->password = $creds[ "password" ];
    }

    protected function cachedTokenAvailable () {
        return Cache::tags( self::CACHE_TAG )->has( self::CACHE_KEY . '_' . $this->getEspAccountId() );
    }

    protected function getCachedToken () {
        return Cache::tags( self::CACHE_TAG )->get( self::CACHE_KEY . '_' . $this->getEspAccountId() );
    }

    protected function cacheNewToken ( $token ) {
        Cache::tags( self::CACHE_TAG )->put(
            self::CACHE_KEY . '_' . $this->getEspAccountId() ,
            $token ,
            Carbon::now()->addMinutes( self::CACHE_TIMEOUT )
        );
    }

    protected function setCampaignId ( $campaignId ) {
        $this->currentCampaignId = $campaignId;
    }

    protected function constructUrl () {
        return self::API_BASE_URL . $this->typeListMap[ $this->callType ];
    }

    protected function constructOptions () {
        if( $this->callType == "auth" ) {
            return $this->defaultRequestOptions + [ "body" => json_encode( [ "Username" => $this->username , "Password" => $this->password ] ) ];
        } elseif ( $this->callType == "listCampaigns" ) {
            return $this->defaultRequestOptions + [
                "body" => json_encode( [
                    "Auth" => [ "Token" => $this->token ] ,
                    "FromSentDate" => Carbon::parse( $this->date )->startOfDay()->format( self::DATE_FORMAT ) ,
                    "ToSentDate" => Carbon::now()->endOfDay()->format( self::DATE_FORMAT )
                ] )
            ];
        } else {
            return $this->defaultRequestOptions + [
                "body" => $this->getStatsRequestBody()
            ];
        }
    }

    protected function getStatsRequestBody () {
        return json_encode( [
            "Auth" => [ "Token" => $this->token ] ,
            "ID" => $this->currentCampaignId
        ] );
    }

    protected function outputFailedDebug ( $exception ) {
        $output = '';
        $request = $exception->getRequest();

        $output .= str_repeat( '=' , 100 ) . "\nFailed API Call\n\n\tRequest Headers:";
        foreach ( $request->getHeaders() as $name => $values ) {
            $output .= "\n\t\t" . $name . ": " . implode( ", " , $values );
        }

        $output .= "\n\n\tRequest URL:\n";
        $output .= "\t\t" . $request->getUri();

        $output .= "\n\n\tRequest Body:\n";
        $output .= "\t\t" . $request->getBody();

        if( $response = $exception->getResponse() ) {
            $output .= "\n\n\tResponse:\n";
            $output .= "\t\t" . $response->getStatusCode() . ':' . $response->getReasonPhrase();
            $output .= "\t\t" . $response->getBody();
        }

        $output .= "\n" . str_repeat( '=' , 100 ) . "\n";

        echo $output;

        return $output;
    }
}
