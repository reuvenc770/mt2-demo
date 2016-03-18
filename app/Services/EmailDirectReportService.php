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
use Log;

/**
 *
 */
class EmailDirectReportService extends AbstractReportService implements IDataService {
    protected $dataRetrievalFailed = false;

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

    public function getUniqueJobId ( $processState ) {
        if ( isset( $processState[ 'campaignId' ] ) && !isset( $processState[ 'recordType' ] ) ) {
            return '::Campaign' . $processState[ 'campaignId' ];
        } elseif ( isset( $processState[ 'campaignId' ] ) && isset( $processState[ 'recordType' ] ) ) {
            return '::Campaign' . $processState[ 'campaignId' ] . '::' . $processState[ 'recordType' ];
        } else {
            return '';
        }
    }

    public function getCampaigns ( $espAccountId , $date ) {
        return $this->reportRepo->getCampaigns( $espAccountId , $date );
    }

    public function splitTypes () {
        return [ 'deliveries' , 'opens' , 'clicks' ];
    }

    public function saveRecords ( &$processState ) {
        $this->dataRetrievalFailed = false;

        switch ( $processState[ 'recordType' ] ) {
            case 'deliveries' :
                try {
                    $deliverables = $this->getDeliveryReport( $processState[ 'campaignId' ] );
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrievee deliverable records. ' . $e->getMessage() );

                    $processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $deliverables as $key => $deliveryRecord ) {
                    $currentEmail = $deliveryRecord[ 'EmailAddress' ];
                    $currentEmailId = $this->emailRecord->getEmailId( $currentEmail );

                    $this->emailRecord->recordDeliverable(
                        $currentEmailId ,
                        $processState[ 'espId' ] ,
                        $processState[ 'campaignId' ] ,
                        $deliveryRecord[ 'ActionDate' ]
                    );
                }
            break;

            case 'opens' :
                try {
                    $opens = $this->getOpenReport( $processState[ 'campaignId' ] );
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrievee open records. ' . $e->getMessage() );

                    $processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $opens as $key => $openRecord ) {
                    $currentEmail = $openRecord[ 'EmailAddress' ];
                    $currentEmailId = $this->emailRecord->getEmailId( $currentEmail );

                    $this->emailRecord->recordOpen(
                        $currentEmailId ,
                        $processState[ 'espId' ] ,
                        $processState[ 'campaignId' ] ,
                        $openRecord[ 'ActionDate' ]
                    );
                }
            break;

            case 'clicks' :
                try {
                    $clicks = $this->getClickReport( $processState[ 'campaignId' ] );
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrievee click records. ' . $e->getMessage() );

                    $processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $clicks as $key => $clickRecord ) {
                    $currentEmail = $clickRecord[ 'EmailAddress' ];
                    $currentEmailId = $this->emailRecord->getEmailId( $currentEmail );

                    $this->emailRecord->recordClick(
                        $currentEmailId ,
                        $processState[ 'espId' ] ,
                        $processState[ 'campaignId' ] ,
                        $clickRecord[ 'ActionDate' ]
                    );
                }
            break;
        }
    }

    public function shouldRetry () { return $this->dataRetrievalFailed; }

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

}
