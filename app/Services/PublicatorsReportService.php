<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\Interfaces\IDataService;
use App\Services\AbstractReportService;

use App\Repositories\ReportRepo;
use App\Services\API\PublicatorsApi;
use App\Services\EmailRecordService;

use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;

use Log;

class PublicatorsReportService extends AbstractReportService implements IDataService {
    public function __construct ( ReportRepo $repo , PublicatorsApi $api , EmailRecordService $emailRecord ) {
        parent::__construct( $repo , $api , $emailRecord );
    }

    public function retrieveApiStats ( $date ) {
        $this->api->setDate( $date );

        if ( !$this->api->isAuthenticated() ) {
            try {
                $this->api->authenticate();
            } catch ( \Exception $e ) {
                throw new JobException( "Failed to Retrieve API Stats. " . $e->getMessage() , JobException::CRITICAL , $e );
            }
        }

        $campaigns = $this->api->getCampaigns();
        $campaignDataCollection = [];

        foreach ( $campaigns as $campaign ) {
            $campaign->esp_account_id = $this->api->getEspAccountId();
            $campaign->internal_id = $campaign->ID;
            unset( $campaign->ID );

            $campaignStats = $this->api->getCampaignStats( $campaign->internal_id ); 

            $campaignDataCollection []= array_merge( (array)$campaign , (array)$campaignStats );
        }

        return $campaignDataCollection;
    }

    public function insertApiRawStats ( $data ) {
        if ( !is_array( $data ) ) {
            throw new JobException( "Parameter 1 must be an array of campaign data." , JobException::NOTICE );
        }

        foreach ( $data as $campaignData ) {
            $this->insertStats( $this->api->getEspAccountId() , $campaignData );
        }

        Event::fire( new RawReportDataWasInserted( $this , $data ) );
    }

    public function mapToRawReport ( $data ) {}

    public function mapToStandardReport ( $data ) {
        $deployId = $this->parseSubID( $data[ "ListName"] );

        return [
            "external_deploy_id" => $deployId , 
            "campaign_name" => $data[ "ListName" ] ,
            "m_deploy_id" => $deployId ,
            "esp_account_id" => $data[ "esp_account_id" ] ,
            "esp_internal_id" => $data[ "internal_id" ] ,
            "datetime" => $data[ "SentDate" ] ,
            "name" => $data[ "ListName" ] ,
            "subject" => $data[ "Subject" ] ,
            "from" => $data[ "FromName" ] ,
            "from_email" => $data[ "FromEmail" ] ,
            "e_sent" => $data[ "TotalMailsSent" ] ,
            "delivered" => $data[ "TotalMailsSent" ] - $data[ "TotalBounces" ] ,
            "bounced" => $data[ "TotalBounces" ] ,
            "optouts" => $data[ "TotalUniqueUnsubscribed" ] ,
            "e_opens" => $data[ "TotalOpened" ] ,
            "e_opens_unique" => $data[ "TotalUniqueOpened" ] ,
            "e_clicks" => $data[ "TotalClicks" ] ,
            "e_clicks_unique" => $data[ "TotalUniqueClicks" ]
        ]; 
    }
}
