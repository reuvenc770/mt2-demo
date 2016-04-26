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

use App\Jobs\Traits\PreventJobOverlapping;

class PublicatorsReportService extends AbstractReportService implements IDataService {
    use PreventJobOverlapping;

    const LOCK_NAME = 'PublicatorsAuth';

    public function __construct ( ReportRepo $repo , PublicatorsApi $api , EmailRecordService $emailRecord ) {
        parent::__construct( $repo , $api , $emailRecord );
    }

    public function retrieveApiStats ( $date ) {
        if ( $this->jobCanRun( self::LOCK_NAME ) ) {
            $this->checkAuthentication();

            $this->api->setDate( $date );

            $campaigns = $this->api->getCampaigns();
            $campaignDataCollection = [];

            foreach ( $campaigns as $campaign ) {
                $campaignRecord = (array)$campaign;

                $campaignRecord[ 'esp_account_id' ] = $this->api->getEspAccountId();
                $campaignRecord[ 'internal_id' ] = $campaignRecord[ 'ID' ];
                unset( $campaignRecord[ 'ID' ] );

                $campaignStats = $this->api->getCampaignStats( $campaignRecord[ 'internal_id' ] ); 

                $campaignStatsRecord = (array)$campaignStats;
                unset( $campaignStatsRecord[ 'ID' ] );

                $campaignDataCollection []= array_merge( $campaignRecord , $campaignStatsRecord );
            }

            return $campaignDataCollection;
        } else {
            throw new JobException( "Job prevented via process lock. Another job is authenticating. " , JobException::NOTICE );
        }
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
                    $jobId .= ( isset( $processState[ "campaign" ] ) ? "::Campaign-" . $processState[ "campaign" ]->esp_internal_id : '' );
                break;

                case 4 :
                    $jobId .= "::Type-" . $processState[ "recordType" ];
                break;
            }
            
            $processState[ "jobIdIndex" ] = $processState[ "currentFilterIndex" ];
            $processState[ "jobId" ] = $jobId;
        }

        return $jobId;
    }

    public function getTypeList ( &$processState ) {
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
        if ( $this->jobCanRun( self::LOCK_NAME ) ) {
            $this->checkAuthentication();

            try {
                if ( $processState[ "recordType" ] == "bounce" ) {
                    $this->processBounces( $processState );

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
        } else {
            throw new JobException( "Job prevented via process lock. Another job is authenticating. " , JobException::NOTICE );
        }
    }

    protected function checkAuthentication () {
        if ( !$this->api->isAuthenticated() ) {
            try {
                $this->createLock( self::LOCK_NAME . $this->api->getEspAccountId() );

                $this->api->authenticate();

                $this->unlock( self::LOCK_NAME . $this->api->getEspAccountId() );
            } catch ( \Exception $e ) {
                throw new JobException( "Failed to Retrieve API Stats. " . $e->getMessage() , JobException::CRITICAL , $e );
            }
        }
    }

    protected function processBounces ( $processState ) {
        $records = $this->api->getRecordStats( PublicatorsApi::TYPE_OPENS_STATS , $processState[ "campaign" ]->esp_internal_id );

        foreach ( $records as $record ) {
            Suppression::recordRawHardBounce(
                $processState[ "espId" ] ,
                $record->Email ,
                $processState[ "campaign" ]->esp_internal_id ,
                '' ,
                $record->TimeStamp
            );
        }
    }
}