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
         
        $campaignListResponse = $this->api->sent( array( 'Since' => $date ) );

        if ( !$campaignListResponse->success() ) return $reportStats;

        $camapaignData = $campaignListResponse->getData();

        foreach ( $campaignData[ 'Items' ] as $campaign ) {
            $campaignDetailsResponse = $this->api->details( $campaign[ 'CampaignID' ] );

            if ( $campaignDetailsResponse->success() ) {
                $reportsStats []=  $campaignDetailsResponse->getData();
            }
        }

        return $reportStats;
    }
}
