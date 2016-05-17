<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;
use App\Repositories\ReportRepo;
use App\Services\API\MaroApi;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Facades\Suppression;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\JobException;
use Carbon\Carbon;

/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class MaroReportService extends AbstractReportService implements IDataService
{
    use InteractsWithQueue;

    protected $actions = ['opens' , 'clicks', 'complaints', 'unsubscribes', 'bounces' ];
    public $pageType = 'opens';
    public $pageNumber = 1;
    public $currentPageData = array();

    public function __construct(ReportRepo $reportRepo, MaroApi $api , EmailRecordService $emailRecord ) {
        parent::__construct( $reportRepo , $api , $emailRecord );
    }

    public function retrieveApiStats($date) {
        $this->api->setDate($date);
        $outputData = array();

        $this->api->constructApiUrl();
        $firstData = $this->api->sendApiRequest();
        $firstData = $this->processGuzzleResult($firstData);

        $outputData = array_merge($outputData, $firstData);

        if (sizeof($firstData) > 0) {
            $pages = (int)$firstData[0]['total_pages'];
            
            if ($pages > 0) {
                $i = 0;
                while ($i <= $pages) {
                    $this->api->constructApiUrl($i);
                    $data = $this->api->sendApiRequest();
                    $data = $this->processGuzzleResult($data);
                    $outputData = array_merge($outputData, $data);
                    $i++;
                }
            }
        }

        $completeData = array();
        foreach ($outputData as $id => $campaign) {
            $campaignId = $campaign['campaign_id'];

            $this->api->constructAdditionalInfoUrl($campaignId);
            $return = $this->api->sendApiRequest();
            $metadata = $this->processGuzzleResult($return);

            $campaign['from_name'] = $metadata['from_name'];
            $campaign['from_email'] = $metadata['from_email']; 
            $campaign['subject'] = $metadata['subject'];     
            $campaign['unique_opens'] = $metadata['unique_opens'];
            $campaign['unique_clicks'] = $metadata['unique_clicks'];
            $campaign['unsubscribes'] = $metadata['unsubscribed'];
            $campaign['complaints'] = $metadata['complaint'];
            $completeData[] = $campaign;
        }
        
        return $completeData;
    }

    public function splitTypes () {
        return [ 'opens' , 'clicks', 'complaints', 'unsubscribes', 'bounces' ];
    }

    public function savePage ( &$processState, $map ) {

        $totalCorrect = 0;
        $totalIncorrect = 0;

        switch ( $processState[ 'recordType' ] ) {
            case 'opens' :
                foreach ( $processState[ 'currentPageData' ] as $key => $opener ) {
                    if (isset($map[ $opener['campaign_id'] ])) {
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_OPENER ,
                            $opener[ 'contact' ][ 'email' ] ,
                            $this->api->getId() ,
                            $map[ $opener['campaign_id'] ],
                            $opener[ 'campaign_id' ] ,
                            $opener[ 'recorded_at' ]
                        );
                        $totalCorrect++;
                    }
                    else {
                        echo "Missed: open with campaign id: {$opener['campaign_id']}" . PHP_EOL;
                        $totalIncorrect++;
                    }

                }
            break;

            case 'clicks' :
                foreach ( $processState[ 'currentPageData' ] as $key => $clicker ) {
                    if (isset($map[ $clicker['campaign_id'] ])) {
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_CLICKER ,
                            $clicker[ 'contact' ][ 'email' ] ,
                            $this->api->getId() ,
                            $map[ $clicker['campaign_id'] ],
                            $clicker[ 'campaign_id' ] ,
                            $clicker[ 'recorded_at' ]
                        );
                        $totalCorrect++;
                    }
                    else {
                        echo "Missed: click with campaign id: {$clicker['campaign_id']}" . PHP_EOL;
                        $totalIncorrect++;
                    }
                }
            break;

            case 'unsubscribes' :
                foreach ( $processState[ 'currentPageData' ] as $key => $unsub ) {
                        Suppression::recordRawUnsub(
                            $this->api->getId() ,
                            $unsub[ 'contact' ][ 'email' ] ,
                            $unsub['campaign_id'] ,
                            "" ,
                            $unsub['recorded_on']
                        );
                }
                break;

            case 'bounces' :
                foreach ( $processState[ 'currentPageData' ] as $key => $bounce ) {
                        Suppression::recordRawHardBounce(
                            $this->api->getId() ,
                            $bounce[ 'contact' ][ 'email' ] ,
                            $bounce['campaign_id'] ,
                            $bounce['diagnostic'] ,
                            $bounce['recorded_on']
                        );

                }
                break;

            case 'complaints' :
                foreach ( $processState[ 'currentPageData' ] as $key => $complainer ) {
                    if (isset($map[ $complainer['campaign_id'] ])) {
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_COMPLAINT ,
                            $complainer[ 'contact' ][ 'email' ] ,
                            $this->api->getId() ,
                            $map[ $complainer['campaign_id'] ],
                            $complainer[ 'campaign_id' ] ,
                            $complainer[ 'recorded_on' ]
                        );
                        $totalCorrect++;
                    }
                    else {
                        $totalIncorrect++;
                    }
                }
                break;
            case 'delivered':
                foreach ( $processState[ 'currentPageData' ] as $key => $delivered ) {
                    if (isset($map[ $delivered['campaign_id'] ])) {
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_DELIVERABLE ,
                            $delivered[ 'email' ] ,
                            $this->api->getId() ,
                            $map[ $delivered['campaign_id'] ],
                            $delivered[ 'campaign_id' ] ,
                            Carbon::parse($delivered[ 'created_at' ])
                        );
                        $totalCorrect++;
                    }
                    else {
                        echo "Missed: deliver with campaign id: {$delivered['campaign_id']}" . PHP_EOL;
                        $totalIncorrect++;
                    }
                }
                break;
        }
        $this->emailRecord->massRecordDeliverables();
        echo "Matched: $totalCorrect; Missing: $totalIncorrect" . PHP_EOL;
        return $totalCorrect;
    }

    public function saveRecords ( &$processState, $map ) {
        $data = $this->api->getDelivered( $processState[ 'campaign' ]->esp_internal_id );
        $data = $this->processGuzzleResult( $data );

        $totalCorrect = 0;
        $totalIncorrect = 0;

        foreach ( $data as $key => $record ) {
            if (isset($map[ $record['campaign_id'] ])) {
                $this->emailRecord->recordDeliverable(
                    self::RECORD_TYPE_DELIVERABLE ,
                    $record[ 'email' ] ,
                    $this->api->getId() ,
                     $map[ $record['campaign_id'] ],
                    $record[ 'campaign_id' ] ,
                    $record[ 'created_at' ]
                );
                $totalCorrect++;
            }
            else {
                echo "Missed: record with campaign id: {record['campaign_id']}" . PHP_EOL;
                $totalIncorrect++;
            }
        }

        echo "Matched: $totalCorrect; Missing: $totalIncorrect" . PHP_EOL;
        return $totalCorrect + $totalIncorrect;
    }

    public function shouldRetry () {
        return false; #releases if guzzle result is not HTTP 200
    }

    public function getUniqueJobId ( &$processState ) {
        $jobId = ( isset( $processState[ 'jobId' ] ) ? $processState[ 'jobId' ] : '' );

        if ( 
            !isset( $processState[ 'jobIdIndex' ] )
            || ( isset( $processState[ 'jobIdIndex' ] ) && $processState[ 'jobIdIndex' ] != $processState[ 'currentFilterIndex' ] )
        ) {
            $filterIndex = $processState[ 'currentFilterIndex' ];
            $pipe = $processState[ 'pipe' ];

            if ( $pipe == 'default' && $filterIndex == 1  ) {
                $jobId .= '::Pipe-' . $pipe . '::' . $processState[ 'recordType' ] . '::Page-' . ( isset( $processState[ 'pageNumber' ] ) ? $processState[ 'pageNumber' ] : 1 );
            } elseif ( $pipe == 'delivered' && $filterIndex == 1 ) {
                $jobId .= ( isset( $processState[ 'campaign' ] ) ? '::Pipe-' .$pipe . '::Campaign-' . $processState[ 'campaign' ]->esp_internal_id : '' );
            }

            $processState[ 'jobIdIndex' ] = $processState[ 'currentFilterIndex' ];
            $processState[ 'jobId' ] = $jobId;
        }

        return $jobId;
    }

    public function setPageType ( $pageType ) {
        if ( in_array( $pageType , [ 'opens' , 'clicks', 'complaints', 'unsubscribes', 'bounces' ] ) ) {
            $this->pageType = $pageType;
        }
    }

    public function setPageNumber ( $pageNumber ) {
        $this->pageNumber = $pageNumber;
    }

    public function getPageNumber () { return $this->pageNumber; }

    public function nextPage () { $this->pageNumber++; }

    public function pageHasData () {
        $this->api->setDeliverableLookBack();
        $this->api->constructDeliverableUrl( $this->pageType , $this->pageNumber );

        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult( $data );
        if ( empty( $data ) ) {
            return false; 
        } else {
            $this->currentPageData = $data;

            return true;
        }
    }

    public function pageHasCampaignData($campaignId) {
        $this->api->setDeliverableLookBack();
        $this->api->setDeliveredUrl($campaignId, $this->pageNumber);

        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult( $data );

        if ( empty( $data ) ) {
            return false; 
        } else {
            $this->currentPageData = $data;
            return true;
        }

    }

    public function getPageData () {
        return $this->currentPageData;
    }

    protected function processGuzzleResult($data) {
        if ( $data->getStatusCode() != 200 ) {
            throw new JobException( 'API call failed.' , JobException::NOTICE );
        }

        $data = $data->getBody()->getContents();
        return json_decode($data, true);
    }

    public function insertApiRawStats($data) {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach($data as $id => $row) {
            $row['esp_account_id'] = $espAccountId;
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToStandardReport($data) { 
        $deployId = $this->parseSubID($data['name']);
        return array(
            'campaign_name' => $data['name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $data['internal_id'],
            'datetime' => $data['sent_at'],
            #'name' => $data[''],
            'subject' => $data['subject'],
            'from' => $data['from_name'],
            'from_email' => $data['from_email'],
            'e_sent' => $data['sent'],
            'delivered' => $data['delivered'],
            'bounced' => (int)$data['bounce'],
            #'optouts' => $data[''],
            'e_opens' => $data['open'],
            'e_opens_unique' => $data['unique_opens'],
            'e_clicks' => $data['click'],
            'e_clicks_unique' => $data['unique_clicks'],
        );
    }

    public function mapToRawReport($data) {
        return array(
            'status' => $data['status'],
            'esp_account_id' => $data['esp_account_id'],
            'internal_id' => (int)$data['campaign_id'],
            'name' => $data['name'],
            'sent' => (int)$data['sent'],
            'delivered' => (int)$data['delivered'],
            'open' => (int)$data['open'],
            'click' => (int)$data['click'],
            'bounce' => (int)$data['bounce'],
            'send_at' => $data['send_at'],
            'sent_at' => $data['sent_at'],
            'maro_created_at' => $data['created_at'],
            'maro_updated_at' => $data['updated_at'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email'],
            'subject' => $data['subject'],
            'unique_opens' => $data['unique_opens'],
            'unique_clicks' => $data['unique_clicks'],
            'unsubscribes' => $data['unsubscribes'],
            'complaints' => $data['complaints'],
        );
    }

    public function pullUnsubsEmailsByLookback($lookback){
        $this->setPageType("unsubscribes");
        $this->setPageNumber(1);
        $return = array();
        while ( $this->pageHasData() ) {
            $records = $this->getPageData();
            $return = array_merge($return, $records);
            $this->nextPage();
        }
        return $return;
    }

    public function insertUnsubs($data, $espAccountId){
        foreach ($data as $entry){
            Suppression::recordRawUnsub($espAccountId,$entry['contact']['email'],$entry['campaign_id'],"", $entry['recorded_on']);
        }
    }

}
