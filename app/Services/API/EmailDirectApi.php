<?php
/**
 *
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;
use App\Facades\Guzzle;
use App\Library\EmailDirect\EmailDirect;
use Carbon\Carbon;

/**
 *
 */
class EmailDirectApi extends EspBaseAPI {
    const DATE_REQUEST_KEY = 'date';
    const DATE_DEFAULT_DAYS_BACK = 1;
    const CAMPAIGN_LIST_KEY = 'Items';
    const CAMPAIGN_ID_KEY = 'CampaignID';
    const API_REQUEST_FIELD_DATE = 'Since';
    const ESP_NAME = "EmailDirect";

    private $api;
    private $date;
    private $campaignList = array();

    public function __construct ($espAccountId) {
        parent::__construct(self::ESP_NAME, $espAccountId );

        $creds = EspApiAccount::grabApiKeyWithSecret( $espAccountId );
  
        $this->api = new EmailDirect( $creds[ 'apiKey' ] );
        $curl = $this->api->getAdapter();
        $curl->setOption(CURLOPT_TIMEOUT,90);
    }

    public function sendAPIRequest () {
        $this->loadCampaignList();
        return $this->getReportStats();
    }

    public function setDate ( $requestData ) {
        if ( !is_null( $requestData[ self::DATE_REQUEST_KEY ] ) ) {
            $this->date = $requestData[ self::DATE_REQUEST_KEY ];
        } else {
            $this->date = Carbon::now()->subDay( self::DATE_DEFAULT_DAYS_BACK )->toDateString();
        }
    }

    private function loadCampaignList () {
        $campaignListResponse = $this->api->campaigns()->sent( array( self::API_REQUEST_FIELD_DATE => $this->date ) );

        if ( !$campaignListResponse->success() ) {
            throw new \Exception( 'Email Direct API Call Failed. ' . $campaignListResponse->getErrorMessage() , $campaignListResponse->getErrorCode() );
        }

        $responseData = $campaignListResponse->getData();

        $this->campaignList = $responseData[ self::CAMPAIGN_LIST_KEY ];
    }

    private function getReportStats () {
        $reportStats = array();

        foreach ( $this->campaignList as $currentCampaign ) {
            $campaignDetailsResponse = $this->api->campaigns( $currentCampaign[ self::CAMPAIGN_ID_KEY ] )->details();

            if ( $campaignDetailsResponse->success() ) {
                $reportStats []= $campaignDetailsResponse->getData();
            } else {
                throw new \Exception( 'Email Direct API Call Failed.' . $campaignDetailsResponse->getErrorMessage() , $campaignDetailsResponse->getErrorCode() );
            }
        }

        return $reportStats;
    }

    public function getDeliveryReport($campaignId,$method){
        $outputData = array();
        $method = strtolower($method);
        $recipientsResponse = $this->api->campaigns($campaignId)->$method(array("PageSize" => 500));

        if(!$recipientsResponse->success()){
            throw new \Exception( "Email Direct API Called Failed.  {$recipientsResponse->getErrorMessage()} :!: {$recipientsResponse->getErrorCode()}");
        }
        $recipientsData = $recipientsResponse->getData();
        $outputData = array_merge($outputData, $recipientsData["Items"]);
        $totalPages = $recipientsData['TotalPages'];

        if ($totalPages > 1) {
            $i = 2;
            while ($i <= $totalPages) {
                $recipientsResponse = $this->api->campaigns($campaignId)->recipients(array("PageNumber" => $i,"PageSize" => 500));
                if(!$recipientsResponse->success()){
                    throw new \Exception( "Email Direct API Called Failed.  {$recipientsResponse->getErrorMessage()} :!: {$recipientsResponse->getErrorCode()}");
                }
                $outputData = array_merge($outputData, $recipientsData["Items"]);
                $i++;
            }
        }
        return $this->reduce_array($outputData,["EmailAddress","ActionDate"]);
    }
}

