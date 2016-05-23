<?php
/**
 *
 */

namespace App\Services;

use App\Services\API\EmailDirectApi;
use App\Repositories\ReportRepo;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Exceptions\JobException;
use App\Facades\Suppression;
use App\Facades\DeployActionEntry;

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
        //$formatedData = $this->mapToRawReport( $data );
        $deployId = $this->parseSubID($data['name']);
        return array(
            'campaign_name' => $data[ 'name' ],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $this->api->getEspAccountId(),
            'esp_internal_id' => $data['internal_id'],
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
            if ( $processState[ 'currentFilterIndex' ] == 2 && isset( $processState[ 'campaign' ] ) ) {
                $jobId .= '::Campaign-' . $processState[ 'campaign' ]->esp_internal_id;
            } elseif ( $processState[ 'currentFilterIndex' ] == 4 && isset( $processState[ 'recordType' ] ) ) {
                $jobId .= '::Type-' . $processState[ 'recordType' ];
            }
            
            $processState[ 'jobIdIndex' ] = $processState[ 'currentFilterIndex' ];
            $processState[ 'jobId' ] = $jobId;
        }

        return $jobId;
    }

    public function getCampaigns ( $espAccountId , $date ) {
        return $this->reportRepo->getCampaigns( $espAccountId , $date );
    }

    public function getTypeList ( $processState ) {
        $typeList = [ 'opens' , 'clicks', "unsubscribes", "complaints", "hardbounces" ];

        if ($this->emailRecord->withinTwoDays( $processState[ 'espAccountId' ] , $processState[ 'campaign' ]->esp_internal_id ) ) {
            $typeList []= 'deliveries';
        }

        return $typeList;
    }

    public function splitTypes ( $processState ) {
        return $processState[ 'typeList' ];
    }

    public function saveRecords ( &$processState, $map ) {
        $type = '';
        // $map is not needed for this version of saveRecords
        $count = 0;
        try {
            switch ( $processState[ 'recordType' ] ) {
                case 'deliveries' :
                    $deliverables = $this->getDeliveryReport( $processState[ 'campaign' ]->esp_internal_id );
                    $count = count($deliverables);
                    foreach ( $deliverables as $key => $deliveryRecord ) {

                            $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_DELIVERABLE ,
                            $deliveryRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->external_deploy_id ,
                            $processState[ 'campaign' ]->esp_internal_id ,
                            $deliveryRecord[ 'ActionDate' ]
                        );
                    }
                    $type = 'deliverable';
                break;

                case 'opens' :
                    $opens = $this->getOpenReport( $processState[ 'campaign' ]->esp_internal_id );
                    $count = count($opens);
                    foreach ( $opens as $key => $openRecord ) {

                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_OPENER ,
                            $openRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->external_deploy_id ,
                            $processState[ 'campaign' ]->esp_internal_id ,
                            $openRecord[ 'ActionDate' ]
                        );

                    }
                    $type = 'open';
                break;

                case 'clicks' :
                    $clicks = $this->getClickReport( $processState[ 'campaign' ]->esp_internal_id );
                    $count = count($clicks);
                    foreach ( $clicks as $key => $clickRecord ) {

                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_CLICKER ,
                            $clickRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->external_deploy_id ,
                            $processState[ 'campaign' ]->esp_internal_id ,
                            $clickRecord[ 'ActionDate' ]
                        );

                    }
                    $type = 'click';
                break;

                case 'unsubscribes' :
                    $unsubs = $this->getUnsubscribeReport( $processState[ 'campaign' ]->esp_internal_id );
                    $count = count($unsubs);
                    foreach ( $unsubs as $key => $unsubRecord ) {
                        Suppression::recordRawUnsub($processState[ 'espId' ] , $unsubRecord[ 'EmailAddress' ],  $processState[ 'campaign' ]->esp_internal_id, "", $unsubRecord[ 'ActionDate' ]);
                    }
                    $type = 'optout';
                break;

                case 'hardbounces' :
                   $unsubs = $this->getUnsubscribeReport( $processState[ 'campaign' ]->esp_internal_id );
                    $count = count($unsubs);
                    foreach ( $unsubs as $key => $hardbounce ) {
                        Suppression::recordRawUnsub($processState[ 'espId' ] , $hardbounce[ 'EmailAddress' ],  $processState[ 'campaign' ]->esp_internal_id,  "", $hardbounce[ 'ActionDate' ]);
                    }
                    $type = 'bounce';
                    break;

                case 'complaints' :
                    $complainers = $this->getComplaintReport( $processState[ 'campaign' ]->esp_internal_id );
                    $count = count($complainers);
                    foreach ( $complainers as $key => $complainerRecord ) {

                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_COMPLAINT ,
                            $complainerRecord[ 'EmailAddress' ] ,
                            $processState[ 'espId' ] ,
                            $processState[ 'campaign' ]->external_deploy_id ,
                            $processState[ 'campaign' ]->esp_internal_id ,
                            $complainerRecord[ 'ActionDate' ]
                        );
                    }
                    $type = 'complaint';
                break;
            }
        } catch ( \Exception $e ) {
            DeployActionEntry::recordFailedRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id, $type);
            $jobException = new JobException( 'Failed to retrieve records. ' . $e->getMessage() , JobException::NOTICE );
            $jobException->setDelay( 180 );
            throw $jobException;
        }
        DeployActionEntry::recordSuccessRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id, $type );
       return $count;
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
