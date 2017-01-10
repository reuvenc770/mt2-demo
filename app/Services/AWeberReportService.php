<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\AWeberEmailAction;
use App\Facades\DeployActionEntry;
use App\Jobs\RetrieveDeliverableReports;
use App\Models\AWeberList;
use App\Models\AWeberReport;
use app\Repositories\AWeberListRepo;
use App\Repositories\ReportRepo;
use App\Services\API\AWeberApi;
use App\Services\Interfaces\IDataService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Event;

/**
 * Class AWeberReportService
 * @package App\Services
 */
class AWeberReportService extends AbstractReportService implements IDataService
{
    /**
     * AWeberReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */
    use DispatchesJobs;

    const DELIVERABLE_LOOKBACK = 2;
    protected $listService;

    /**
     * AWeberReportService constructor.
     * @param ReportRepo $reportRepo
     * @param AWeberApi $api
     * @param EmailRecordService $emailRecord
     */
    public function __construct(ReportRepo $reportRepo, AWeberApi $api, EmailRecordService $emailRecord)
    {
        parent::__construct($reportRepo, $api, $emailRecord);
        //tightly coupled but OK since it will never really be replaced or used outside of context
        $this->listService = new AWeberListService(new AWeberListRepo(new AWeberList()));
    }

    /**
     * @param $date
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function retrieveApiStats($date)
    {
        $date = null; //unfortunately date does not matter here.
        $campaignData = array();
        $activeLists = $this->listService->getActiveLists();
        $campaigns = $this->api->getCampaigns($activeLists);
        
        foreach ($campaigns as $campaign) {
            $clickEmail = -1;
            $openEmail = -1;
            $row = array_merge($campaign, ["unique_clicks" => $clickEmail, "unique_opens" => $openEmail]);
            $campaignData[] = $row;
        }

        return $campaignData;
    }

    
    public function getMailingLists(){
        return $this->api->makeApiRequest("lists", array("ws.size" => 100));
    }

    /**
     * @param $xmlData
     */
    public function insertApiRawStats($data)
    {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach($data as $row) {
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }
        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToRawReport($data)
    {
        return array(
            "internal_id" => $data['internal_id'],
            "esp_account_id" => $this->api->getEspAccountId(),
            "info_url"  => $data['info_url'],
            "subject" => $data['subject'],
            "datetime" => $data['sent_at'],
            "total_sent" => $data['total_sent'],
            "total_opens" => $data['total_opens'],
            "total_unsubscribes" => $data['total_unsubscribes'],
            "total_clicks" => $data['total_clicks'],
            "total_undelivered" => $data['total_undelivered'],
            "unique_clicks" => $data['unique_clicks'],
            "unique_opens" => $data['unique_opens'],

        );
    }

    public function mapToStandardReport($data)
    {
        return array(
            'campaign_name' => "",
            'external_deploy_id' => 0,
            'm_deploy_id' => 0,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $data['internal_id'],
            'datetime' => $data['datetime'],
            'name' => "",
            'subject' => $data['subject'],
            'from' => "",
            'from_email' => "",
            'delivered' => $data[ 'total_sent' ],
            'bounced' => "",
            'e_opens' => $data[ 'total_opens' ],
            'e_opens_unique' => "",
            'e_clicks' => $data[ 'total_clicks' ],
            'e_clicks_unique' => "",
        );
    }

    public function getUniqueStatForCampaignUrl($url, $type){
        switch ($type){
            case AWeberReport::UNIQUE_OPENS:
                $fullUrl = "{$url}/stats/unique_opens";
                $return = $this->api->getStateValueFromUrl($fullUrl);
                break;
            case AWeberReport::UNIQUE_CLICKS:
                $fullUrl = "{$url}/stats/unique_clicks";
                $return = $this->api->getStateValueFromUrl($fullUrl);
                break;
            default:
                throw new JobException("Not a valid action type");
        }
        return $return;
    }
    
    public function updateUniqueStatForCampaignUrl($id, $type, $value){
        switch ($type){
            case AWeberReport::UNIQUE_OPENS:
                $this->reportRepo->updateStatCount($id, "unique_opens", $value);
                break;
            case AWeberReport::UNIQUE_CLICKS:
                $this->reportRepo->updateStatCount($id,"unique_clicks", $value);
                break;
            default:
                throw new JobException("Not a valid action type");
        }
    }

    public function splitTypes($processState){
        return ['delivers','links'];
    }


    public function pushRecords(array $records, $targetId) {}

    public function getUniqueJobId ( &$processState ) {
        $jobId = ( isset( $processState[ 'jobId' ] ) ? $processState[ 'jobId' ] : '' );

        if (
            !isset( $processState[ 'jobIdIndex' ] )
            || ( isset( $processState[ 'jobIdIndex' ] ) && $processState[ 'jobIdIndex' ] != $processState[ 'currentFilterIndex' ] )
        ) {
            switch ($processState['currentFilterIndex']) {
                case 1 :
                    $jobId .= "::{$processState['espAccountId']}";
                    break;
            }

            $processState['jobIdIndex'] = $processState['currentFilterIndex'];
            $processState['jobId'] = $jobId;
        }
        return $jobId;
    }



    public function saveRecords(&$processState) {
        $type = "";
        $count = 0;
        $espInternalId = $processState['campaign']->esp_internal_id;
        // sometimes this doesn't work - if we don't have the campaign saved
        $deployId = $processState['campaign']->external_deploy_id;

        try {
            switch ( $processState[ 'recordType' ] ) {

                case 'delivers' :
                    $report = $this->getRawReportByInternalId($espInternalId);
                    $statUrl = "{$report->info_url}/messages";
                    $messages = $this->api->makeApiRequest($statUrl,array( "ws.size" => 100),true);

                    foreach($messages as $message) {
                        AWeberEmailAction::queueDeliverable(self::RECORD_TYPE_DELIVERABLE, $message->subscriber_link, $this->api->getEspAccountId(), $deployId, $espInternalId, $message->event_time);

                        if($message->total_opens > 0){
                            $processState[ 'openCollection' ] = $message->opens_collection_link;
                            $processState[ 'recordType' ] =  'opens';
                            $job = new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $processState[ 'recordType' ], str_random(16), $processState);
                            $this->dispatch($job);
                        }

                        $processState[ 'recordType' ] =  'deliverable';
                    }

                    AWeberEmailAction::massRecordDeliverables();
                    $type = "deliverable";
                    break;

                case 'opens' :
                    $messages = $this->api->makeApiRequest($processState[ 'openCollection' ],array(),true);
                    foreach($messages as $message) {
                        AWeberEmailAction::queueDeliverable(self::RECORD_TYPE_OPENER, $message->subscriber_link, $this->api->getEspAccountId(), $deployId, $espInternalId, $message->event_time);
                    }
                    AWeberEmailAction::massRecordDeliverables();
                    $type = "open";
                    break;

                case 'links' :
                    $report = $this->getRawReportByInternalId($espInternalId);
                    $linkUrl = "{$report->info_url}/links";
                    $urls = $this->api->makeApiRequest($linkUrl,array(),true);

                    foreach($urls as $message) {
                        if($message->total_clicks > 0) {
                            $processState['clickCollection'] = $message->clicks_collection_link;
                            $processState['recordType'] = 'clicks';
                            $job = new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $processState['recordType'], str_random(16), $processState);
                            $this->dispatch($job);
                        }
                            $processState[ 'recordType' ] =  'links';
                    }
                    $type = "click";
                    break;

                case 'clicks' :
                    $messages = $this->api->makeApiRequest($processState[ 'clickCollection' ],array( "ws.size" => 100),true);
                    foreach($messages as $message) {
                        AWeberEmailAction::queueDeliverable(self::RECORD_TYPE_CLICKER, $message->subscriber_link, $this->api->getEspAccountId(), $deployId, $espInternalId, $message->event_time);
                    }
                    AWeberEmailAction::massRecordDeliverables();
                    $type = "click";
                    break;
            }
            
            DeployActionEntry::recordSuccessRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id, $type );
        } catch ( \Exception $e ) {
            DeployActionEntry::recordFailedRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id, $type);
            $jobException = new JobException( 'Failed to save records. ' . $e->getMessage() , JobException::NOTICE , $e );
            $jobException->setDelay( 180 );
            throw $jobException;
        }
        return $count;
    }

    public function getRawReportByInternalId($internalId){
        return $this->reportRepo->getRowByExternalId($internalId);
    }

}
