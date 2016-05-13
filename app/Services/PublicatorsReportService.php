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

use Cache;

use Carbon\Carbon;

class PublicatorsReportService extends AbstractReportService implements IDataService {
    const CACHE_TIMEOUT = 4; #in mins
    const CACHE_TAG = "publicators";
    const LOCK_NAME = 'PublicatorsAuth';

    public function __construct ( ReportRepo $repo , PublicatorsApi $api , EmailRecordService $emailRecord ) {
        parent::__construct( $repo , $api , $emailRecord );
    }

    public function retrieveApiStats ( $date ) {
        if ( $this->lockFound() ) {
            throw new JobException( "Job prevented via process lock. Another job is authenticating. " , JobException::NOTICE );
        }

        try {
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
        } catch ( \Exception $e ) {
            throw new JobException( "Failed to Retrieve Api Stats. " . $e->getMessage() , JobException::ERROR , $e );
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

        if($this->emailRecord->withinTwoDays( $processState[ "espAccountId" ] , $processState[ "campaign" ]->esp_internal_id) || 'rerun' === $processState['pipe'] ){
            $typeList[] = "sent";
        }

        return $typeList;
    }

    public function splitTypes ( $processState ) {
        return $processState[ "typeList" ];
    }

    public function saveRecords ( $processState ) {
        if ( $this->lockFound() ) {
            $pubException = new JobException( "Job prevented via process lock. Another job is authenticating. " , JobException::NOTICE );
            $pubException->setDelay( 300 );
            throw $pubException;
        }

        $this->checkAuthentication();

        try {
            if ( $processState[ "recordType" ] == "bounce" ) {
                $this->processBounces( $processState );

                return true;
            }

            if ( $processState[ "recordType" ] == "unsub" ) {
                $this->processUnsubs( $processState );

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

            }
        } catch ( \Exception $e ) {
            throw new JobException( "Failed to Retrieve Email Record Stats. " . $e->getMessage() , JobException::ERROR , $e );
        }

        try {
            // Set deploy id
            $deployId = $processState["campaign"]->external_deploy_id;

            foreach ( $records as $record ) {

                // Need to find cases without seconds and provide up an appropriate second
                if (preg_match('/\s\d{2}\:\d{2}$/', $record->TimeStamp)) {
                    $key = md5($record->Email . $deployId . $recordType . $record->TimeStamp);

                    // If the tag already exists, get the (already-incremented) second, and increment again
                    if (Cache::tags([$recordType, $deployId])->has($key)) {
                        $count = Cache::tags([$recordType, $deployId])->get($key);
                        Cache::tags([$recordType, $deployId])->increment($key);
                    }
                    else {
                        // Tag does not exist. Create it with an an appropriate for 30 min.
                        $count = 0;
                        Cache::tags([$recordType, $deployId])->put($key, 1, 30);
                    }

                    // Set up the new timestamp
                    $padding = $count < 10 ? '0' : '';
                    $timeStamp = $record->TimeStamp . ':' . $padding . $count;
                }
                else {
                    $timeStamp = $record->TimeStamp;
                }


                $this->emailRecord->queueDeliverable(
                    $recordType , 
                    $record->Email ,
                    $processState[ "espId" ] ,
                    $processState[ "campaign" ]->external_deploy_id ,
                    $processState[ "campaign" ]->esp_internal_id ,
                    $timeStamp
                );
            }

            $this->emailRecord->massRecordDeliverables();
        }
        catch (\Exception $e) {
            $jobException = new JobException( 'Failed to insert publicators deliverables.  ' . $e->getMessage() , JobException::WARNING , $e );
            throw $jobException;
        }
    }

    protected function checkAuthentication () {
        if ( !$this->api->isAuthenticated() ) {
            try {
                $this->lock();

                $this->api->authenticate();

                $this->unlock();
            } catch ( \Exception $e ) {
                throw new JobException( "Failed to Retrieve API Stats. " . $e->getMessage() , JobException::CRITICAL , $e );
            }
        }
    }

    protected function processBounces ( $processState ) {
        $records = $this->api->getRecordStats( PublicatorsApi::TYPE_BOUNCES_STATS , $processState[ "campaign" ]->esp_internal_id );

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

    protected function processUnsubs ( $processState ) {
        $records = $this->api->getRecordStats( PublicatorsApi::TYPE_UNSUBSCRIBED_STATS , $processState[ "campaign" ]->esp_internal_id );

        foreach ( $records as $record ) {
            Suppression::recordRawUnsub(
                $processState[ "espId" ] ,
                $record->Email ,
                $processState[ "campaign" ]->esp_internal_id ,
                '' ,
                $record->TimeStamp
            );
        }
    }

    protected function lockFound () {
        return Cache::has( self::LOCK_NAME . '_' . $this->api->getEspAccountId() );
    }

    protected function lock () {
        Cache::put(
            self::LOCK_NAME . '_' . $this->api->getEspAccountId() ,
            1 ,
            Carbon::now()->addMinutes( self::CACHE_TIMEOUT )
        );
    }

    protected function unlock () {
        Cache::forget( self::LOCK_NAME . '_' . $this->api->getEspAccountId() );
    }
}
