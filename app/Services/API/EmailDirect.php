<?php
/**
 *
 */

namespace App\Services\API;

use App\Facades\EspAccount;
use App\Facades\Guzzle;
use Carbon\Carbon;
use EmailDirect\EmailDirect

/**
 *
 */
class EmailDirectApi extends BaseAPI {
    CONST API_URL = 'https://rest.emaildirect.com/v1';

    private $api;

    public function __construct ( $name , $accountNumber ) {
        parent::__construct( $name , $accountNumber );

        $creds = EspAccount::grabApiKeyWithSecret( $accountNumber );
  
        $this->api = new EmailDirect( $creds[ 'apiKey' ] );
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
                $campaignDetails = $campaignDetailsResponse->getData();

                $reportsStats []= array(
                    'internal_id' => $campaignDetails[ 'CampaignID' ] ,
                    'campaign_id' => $campaignDetails[ 'CampaignID' ] ,
                    'campaign_name' => $campaignDetails[ 'Name' ] ,
                    'status' => $campaignDetails[ 'Stats' ] ,
                    'is_active' => $campaignDetails[ 'IsActive' ] ,
                    'created' => $campaignDetails[ 'Created' ] ,
                    'scheduled_date' => $campaignDetails[ 'ScheduledDate' ] ,
                    'fron_name' => $campaignDetails[ 'FromName' ] ,
                    'from_email' => $campaignDetails[ 'FromEmail' ] ,
                    'to_name' => $campaignDetails[ 'ToName' ] ,
                    'creative_id' => $campaignDetails[ 'CreativeId' ] ,
                    'target' => $campaignDetails[ 'Target' ] ,
                    'subject' => $campaignDetails[ 'Subject' ] ,
                    'archive_url' => $campaignDetails[ 'ArchiveURL' ] ,
                    'emails_sent' => $campaignDetails[ 'EmailsSent' ] ,
                    'opens' => $campaignDetails[ 'Opens' ] ,
                    'unique_clicks' => $campaignDetails[ 'UniqueClicks' ] ,
                    'total_clicks' => $campaignDetails[ 'TotalClicks' ] ,
                    'removes' => $campaignDetails[ 'Removes' ] ,
                    'forwards' => $campaignDetails[ 'Forwards' ] ,
                    'forwards_from' => $campaignDetails[ 'ForwardsFrom' ] ,
                    'hard_bounces' => $campaignDetails[ 'HardBounces' ] ,
                    'soft_bounces' => $campaignDetails[ 'SoftBounces' ] ,
                    'complaints' => $campaignDetails[ 'Complaints' ] ,
                    'delivered' => $campaignDetails[ 'Delivered' ] ,
                    'delivery_rate' => $campaignDetails[ 'DeliveryRate' ] ,
                    'open_rate' => $campaignDetails[ 'OpenRate' ] ,
                    'unique_rate' => $campaignDetails[ 'unique_rate' ] ,
                    'ctr' => $campaignDetails[ 'CTR' ] ,
                    'remove_rate' => $campaignDetails[ 'RemoveRate' ] , 
                    'bounce_rate' => $campaignDetails[ 'BounceRate' ] ,
                    'soft_bounce_rate' => $campaignDetails[ 'SoftBounceRate' ] ,
                    'complaint_rate' => $campaignDetails[ 'ComplaintRate' ]
                );
            }
        }
    }
}
