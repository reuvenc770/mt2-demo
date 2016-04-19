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

        foreach ( $campaigns as $campaignId ) {
            $campaignData = $this->api->getCampaignStats( $campaignId ); 

            $campaignDataCollection []= [
                "esp_account_id" => $this->api->getEspAccountId() ,
                "internal_id" => (int)$campaignData->ID ,
                "sent_date" => $campaignData->SentDate ,
                "total_sent" => (int)$campaignData->TotalMailsSent ,
                "total_opens" => (int)$campaignData->TotalOpened ,
                "total_clicks" => (int)$campaignData->TotalClicks ,
                "total_bounces" => (int)$campaignData->TotalBounces ,
                "total_unsubscribes" => (int)$campaignData->TotalUniqueUnsubscribed
            ];
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

        #Event::fire( new RawReportDataWasInserted( $this , $data ) );
    }

    public function mapToRawReport ( $data ) {}

    public function mapToStandardReport ( $data ) {}
}
