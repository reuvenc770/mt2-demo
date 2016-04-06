<?php
/**
 *
 */

namespace App\Services;
use App\Services\API\EspBaseApi;
use App\Services\API\EmailDirectApi;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Services\EmailRecordService;
use App\Exceptions\JobException;
use Log;

/**
 *
 */
class EmailDirectReportService extends AbstractReportService implements IDataService {
    private $invalidFields = array( 'Publication' , 'Links' );

    public function __construct ( ReportRepo $reportRepo , EmailDirectApi $api , EmailRecordService $emailRecord ) {
        parent::__construct($reportRepo, $api , $emailRecord );
    }

    public function retrieveApiStats ( $date ) {
        try {
            $this->api->setDate(array( 'date' => $date ));
            return $this->api->sendApiRequest();
        } catch ( Exception $e ) {
            throw $e;
        }
    }

    public function insertApiRawStats ( $rawStats ) {
        $convertedRecordCollection = array();
        $espAccountId = $this->api->getEspAccountId();

        foreach ( $rawStats as $rawCampaignStats ) {
            $convertedRecord = $this->mapToRawReport( $rawCampaignStats );
            $this->insertStats( $espAccountId , $convertedRecord );
            $convertedRecordCollection []= $convertedRecord;
        }

        Event::fire(new RawReportDataWasInserted($this, $convertedRecordCollection ) );
    }

    public function mapToStandardReport ( $data ) {
        $formatedData = $this->mapToRawReport( $data );

        return array(
            'deploy_id' => $data[ 'name' ],
            'sub_id' => $this->parseSubID($data['name']),
            'esp_account_id' => $this->api->getEspAccountId(),
            'datetime' => $data[ 'scheduled_date' ],
            'name' => $data[ 'campaign_id' ],
            'subject' => $data[ 'subject' ],
            'from' => $data[ 'from_name' ],
            'from_email' => $data[ 'from_email' ],
            'delivered' => $data[ 'delivered' ],
            'bounced' => (int)$data['hard_bounces'],
            'e_opens' => $data[ 'opens' ],
            'e_clicks' => $data[ 'total_clicks' ],
            'e_clicks_unique' => $data[ 'unique_clicks' ],
        );
    }

    public function mapToRawReport ( $rawData ) {
        $formattedData = array();

        array_walk( $rawData , function ( $campaignDatapoint , $key ) use ( &$formattedData ) {
            $isValidField = !in_array( $key , $this->invalidFields );

            if ( $isValidField ) {
                switch ( $key ) {
                    case 'CampaignID' :
                        $formattedData[ 'campaign_id' ] = $campaignDatapoint;
                        $formattedData[ 'internal_id' ] = $campaignDatapoint;
                        break;

                    case 'ArchiveURL' :
                        $formattedData[ 'archive_url' ] = $campaignDatapoint;
                        break;

                    case 'CTR' :
                        $formattedData[ 'ctr' ] = $campaignDatapoint;
                        break;

                    case 'Creative' :
                        $formattedData[ 'creative_id' ] = $campaignDatapoint[ 'CreativeID' ];
                        break;

                    default :
                        $formattedData[ snake_case( $key ) ] = $campaignDatapoint;
                }
            }
        } );

        return $formattedData;
    }

    public function getUniqueJobId ( &$processState ) {
        $jobId = ( isset( $processState[ 'jobId' ] ) ? $processState[ 'jobId' ] : '' );

        if ( 
            !isset( $processState[ 'jobIdIndex' ] )
            || ( isset( $processState[ 'jobIdIndex' ] ) && $processState[ 'jobIdIndex' ] != $processState[ 'currentFilterIndex' ] )
        ) {
            switch ( $processState[ 'currentFilterIndex' ] ) {
                case 1 :
                    $jobId .= '::Campaign-' . $processState[ 'campaign' ]->internal_id;
                break;

                case 2 :
                    $jobId .= '::Type-' . $processState[ 'recordType' ];
                break;
            }
            
            $processState[ 'jobIdIndex' ] = $processState[ 'currentFilterIndex' ];
            $processState[ 'jobId' ] = $jobId;
        }

        return $jobId;
    }

    public function getCampaigns ( $espAccountId , $date ) {
        return $this->reportRepo->getCampaigns( $espAccountId , $date );
    }

    public function splitTypes () {
        return [ 'deliveries' , 'opens' , 'clicks', "unsubscribes", "complaints" ];
    }

    public function saveRecords ( &$processState ) {
        try {
            switch ( $processState[ 'recordType' ] ) {
                case 'deliveries' :
                    $deliverables = $this->getDeliveryReport( $processState[ 'campaign' ]->internal_id );

                    foreach ( $deliverables as $key => $deliveryRecord ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_DELIVERABLE ,
                            $deliveryRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->internal_id ,
                            $deliveryRecord[ 'ActionDate' ]
                        );
                    }
                break;

                case 'opens' :
                    $opens = $this->getOpenReport( $processState[ 'campaign' ]->internal_id );

                    foreach ( $opens as $key => $openRecord ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_OPENER ,
                            $openRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->internal_id ,
                            $openRecord[ 'ActionDate' ]
                        );
                    }
                break;

                case 'clicks' :
                    $clicks = $this->getClickReport( $processState[ 'campaign' ]->internal_id );

                    foreach ( $clicks as $key => $clickRecord ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_CLICKER ,
                            $clickRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->internal_id ,
                            $clickRecord[ 'ActionDate' ]
                        );
                    }
                break;

                case 'unsubscribes' :
                    $unsubs = $this->getUnsubscribeReport( $processState[ 'campaign' ]->internal_id );

                    foreach ( $unsubs as $key => $unsubRecord ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_UNSUBSCRIBE ,
                            $unsubRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->internal_id ,
                            $unsubRecord[ 'ActionDate' ]
                        );
                    }
                break;

                case 'complaints' :
                    $complainers = $this->getComplaintReport( $processState[ 'campaign' ]->internal_id );

                    foreach ( $complainers as $key => $complainerRecord ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_COMPLAINT ,
                            $complainerRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->internal_id ,
                            $complainerRecord[ 'ActionDate' ]
                        );
                    }
                break;
            }
        } catch ( \Exception $e ) {
            $jobException = new JobException( 'Failed to retrieve records. ' . $e->getMessage() , JobException::NOTICE );
            $jobException->setDelay( 180 );
            throw $jobException;
        } catch ( Exception $e ) {
            $jobException = new JobException( 'Failed to retrieve records. ' . $e->getMessage() , JobException::NOTICE );
            $jobException->setDelay( 180 );
            throw $jobException;
        }
    }

    public function getDeliveryReport($campaignId){
      return  $this->api->getDeliveryReport($campaignId, "Recipients");
    }

    public function getOpenReport($campaignId){
      return  $this->api->getDeliveryReport($campaignId, "Opens");
    }

    public function getClickReport($campaignId){
      return  $this->api->getDeliveryReport($campaignId, "Clicks");
    }

    public function getUnsubscribeReport($campaignId){
      return  $this->api->getDeliveryReport($campaignId, "Removes");
    }
    //Todo do we include softbounces?
    public function getHardBounceReport($campaignId){
       return $this->api->getDeliveryReport($campaignId, "HardBounces");
    }

    public function getComplaintReport($campaignId){
        return $this->api->getDeliveryReport($campaignId, "Complaints");
    }

}
