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
use App\Facades\Suppression;

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

    public function getUniqueJobId ( &$processState ) {
        $jobId = ( isset( $processState[ "jobId" ] ) ? $processState[ "jobId" ] : "" );

        if ( 
            !isset( $processState[ "jobIdIndex" ] )
            || ( isset( $processState[ "jobIdIndex" ] ) && $processState[ "jobIdIndex" ] != $processState[ "currentFilterIndex" ] )
        ) {
            switch ( $processState[ "currentFilterIndex" ] ) {
                case 2 :
                    $jobId .= "::Campaign-" . $processState[ "campaign" ]->esp_internal_id;
                break;

                case 6 :
                    $jobId .= "::Types-" . join( "," , $processState[ "typeList" ] );
                break;
            }
            
            $processState[ "jobIdIndex" ] = $processState[ "currentFilterIndex" ];
            $processState[ "jobId" ] = $jobId;
        }

        return $jobId;
    }

    public function getTypeList ( &$processState ) {
        return [ 'open' ];

        $typeList = [ "open" , "click" , "unsub" , "bounce" ];

        if( !$this->emailRecord->checkForDeliverables( $processState[ "espAccountId" ] , $processState[ "campaign" ]->esp_internal_id ) ){
            $typeList[] = "sent";
        }

        return $typeList;
    }

    public function splitTypes ( $processState ) {
        return $processState[ "typeList" ];
    }

    public function saveRecords ( $processState ) {
        if ( !$this->api->isAuthenticated() ) {
            try {
                $this->api->authenticate();
            } catch ( \Exception $e ) {
                throw new JobException( "Failed to Retrieve API Stats. " . $e->getMessage() , JobException::CRITICAL , $e );
            }
        }

        try {
            if ( $processState[ "recordType" ] == "bounce" ) {
                $this->processBounces( $processState[ "campaign" ]->esp_internal_id );

                return true;
            }

            $records = [];
            $recordType = "";

            switch ( $processState[ "recordType" ] ) {
                case "sent" :
                    $records = $this->api->getRecordStats( PublicatorsApi::TYPE_SENT_STATS , $processState[ "campaign" ]->esp_internal_id );
                    $recordType = self::RECORD_TYPE_DELIVERABLE;
                break;

                case "open" :
                    $records = $this->api->getRecordStats( PublicatorsApi::TYPE_OPENS_STATS , $processState[ "campaign" ]->esp_internal_id );
                    $recordType = self::RECORD_TYPE_OPENER;
                break;

                case "click" :
                    $records = $this->api->getRecordStats( PublicatorsApi::TYPE_CLICKS_STATS , $processState[ "campaign" ]->esp_internal_id );
                    $recordType = self::RECORD_TYPE_CLICKER;
                break;

                case "unsub" :
                    $records = $this->api->getRecordStats( PublicatorsApi::TYPE_UNSUBSCRIBED_STATS , $processState[ "campaign" ]->esp_internal_id );
                    $recordType = self::RECORD_TYPE_UNSUBSCRIBE;
                break;
            }
        } catch ( \Exception $e ) {
            throw new JobException( "Failed to Retrieve Email Record Stats. " . $e->getMessage() , JobException::ERROR , $e );
        }

        die();
        foreach ( $records as $record ) {
            $this->emailRecord->recordDeliverable(
                $recordType , 
                $record->Email ,
                $processState[ "espId" ] ,
                $processState[ "campaign" ]->external_deploy_id ,
                $processState[ "campaign" ]->esp_internal_id ,
                $record->TimeStamp
            ); 
        }
    }

    protected function processBounces () {
        $records = $this->api->getRecordStats( PublicatorsApi::TYPE_OPENS_STATS , $processState[ "campaign" ]->esp_internal_id );

        foreach ( $records as $record ) {
            Suppression::recordRawHardBounce(
                $processState[ "ticket" ][ "espId" ] ,
                $record->Email ,
                $processState[ "ticket" ][ "espInternalId" ] ,
                $reason ,
                $record->TimeStamp
            );
        }
    }
}
