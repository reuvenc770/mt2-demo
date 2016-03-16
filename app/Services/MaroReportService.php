<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;
use App\Repositories\ReportRepo;
use App\Services\API\MaroApi;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Services\EmailRecordService;
use Log;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class MaroReportService extends AbstractReportService implements IDataService
{
    use InteractsWithQueue;

    protected $actions = ['opens', 'clicks', 'bounces', 'complaints', 'unsubscribes'];

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
        return [ 'opens' , 'clicks' ];
    }

    public function saveRecords ( &$processState ) {
        switch ( $processState[ 'recordType' ] ) {
            case 'opens' :
                $openData = $this->getDeliveredRecords( 'opens' );

                foreach ( $openData as $key => $openner ) {
                    $this->emailRecord->recordOpen(
                        $this->emailRecord->getEmailId( $openner[ 'contact' ][ 'email' ] ) ,
                        $this->api->getId() ,
                        $openner[ 'campaign_id' ] ,
                        $openner[ 'recorded_at' ]
                    );
                }
            break;

            case 'clicks' :
                $clickData = $this->getDeliveredRecords( 'clicks' );

                foreach ( $clickData as $key => $clicker ) {
                    $this->emailRecord->recordClick(
                        $this->emailRecord->getEmailId( $clicker[ 'contact' ][ 'email' ] ) ,
                        $this->api->getId() ,
                        $clicker[ 'campaign_id' ] ,
                        $clicker[ 'recorded_at' ]
                    );
                }
            break;
        }
    }

    public function shouldRetry () {
        return false; #releases if guzzle result is not HTTP 200
    }

    public function getDeliveredRecords ( $action ) {
        $outputData = array();
        $this->api->setDeliverableLookBack();

        $dataFound = true;
        $page = 1;

        while ($dataFound) {
            $this->api->constructDeliverableUrl($action, $page);
            $data = $this->api->sendApiRequest();

            $data = $this->processGuzzleResult($data);

            if (empty($data)) {
                $dataFound = false;
            }
            else {
                $outputData = array_merge($outputData, $data);
                $page++;
            }
        }       

        return $outputData;
    }

    protected function processGuzzleResult($data) {
        if ( $data->getStatusCode() != 200 ) $this->release( 60 );

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
        return array(

            'deploy_id' => $data['name'],
            'sub_id' => $this->parseSubID($data['name']),
            'm_deploy_id' => 0, // temporarily 0 until deploys are created
            'esp_account_id' => $data['esp_account_id'],
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

}
