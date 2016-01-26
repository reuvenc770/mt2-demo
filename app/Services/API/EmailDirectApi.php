<?php
/**
 *
 */

namespace App\Services\API;

use App\Facades\EspAccount;
use App\Facades\Guzzle;
use Carbon\Carbon;

/**
 *
 */
class EmailDirectApi extends BaseAPI {
    private $api;

    public function __construct ( $name , $accountNumber ) {
        parent::__construct( $name , $accountNumber );

        $creds = EspAccount::grabApiKeyWithSecret( $accountNumber );
  
        $this->api = new \EmailDirect( $creds[ 'apiKey' ] );
    }

    protected function sendAPIRequest ( $data ) {
        $reportStats = array();
        $date = null;

        if ( !is_null( $data[ 'date' ] ) ) $date = $data[ 'date' ];
        else $date = Carbon::now()->subDay(1)->toDateString();
         
        $campaignListResponse = $this->api->campaigns()->sent( array( 'Since' => "01-25-2016" ) );

        if ( !$campaignListResponse->success() ) return $reportStats;

        $responseData = $campaignListResponse->getData();

        $campaignData = $responseData[ 'Items' ];

        foreach ( $campaignData as $currentCampaign ) {
            $campaignDetailsResponse = $this->api->campaigns( $currentCampaign[ 'CampaignID' ] )->details();

            if ( $campaignDetailsResponse->success() ) {
                $reportStats []= $campaignDetailsResponse->getData();
            }
        }

        return $reportStats;
    }
}
