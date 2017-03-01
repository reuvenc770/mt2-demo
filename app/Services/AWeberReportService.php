<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\AWeberEmailAction;
use App\Factories\APIFactory;
use App\Jobs\RetrieveDeliverableReports;
use App\Models\AWeberList;
use App\Models\AWeberReport;
use App\Repositories\AWeberListRepo;
use App\Repositories\ReportRepo;
use App\Services\API\AWeberApi;
use App\Services\Interfaces\IDataService;
use Cache;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;
use App\Jobs\UpdateSingleAWeberSubscriber;
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
    use InteractsWithQueue;

    const DELIVERABLE_LOOKBACK = 2;
    public $pageNumber = 1;
    public $currentPageData = array();
    public $pageType = '';
    protected $listService;
    protected $standardService;

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
        $this->standardService = APIFactory::createSimpleStandardReportService();
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
        $activeLists = $this->listService->getActiveLists($this->api->getEspAccountId());
        $campaigns = $this->api->getCampaigns($activeLists, $this->limit);

        foreach ($campaigns as $campaign) {
            //using -1 because we need a way to know when a report has not been picked up yet for click/unique pull
            $clickEmail = -1;
            $openEmail = -1;
            $row = array_merge($campaign, ["unique_clicks" => $clickEmail, "unique_opens" => $openEmail]);
            $campaignData[] = $row;
        }

        return $campaignData;
    }


    public function getMailingLists()
    {
        return $this->api->makeApiRequest("lists", array("ws.size" => 100));
    }

    /**
     * @param $xmlData
     */
    public function insertApiRawStats($data)
    {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach ($data as $row) {
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[] = $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToRawReport($data)
    {
        $newRawRecord = array(
            "internal_id" => $data['internal_id'],
            "esp_account_id" => $this->api->getEspAccountId(),
            "info_url" => $data['info_url'],
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

        //Adding campaign name so the record gets saved to standard report properly
        $existingRawRecord = $this->reportRepo->getRowByExternalId($data['internal_id']);

        if (!is_null($existingRawRecord)) {
            $newRawRecord["campaign_name"] = $existingRawRecord["campaign_name"];

            if ($existingRawRecord["campaign_name"] != "") {
                $campaignNameParts = explode("_", $existingRawRecord["campaign_name"]);
                $newRawRecord["deploy_id"] = $campaignNameParts[0];
            }
        }

        return $newRawRecord;
    }

    public function getUniqueStatForCampaignUrl($url, $type)
    {
        switch ($type) {
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

    public function updateUniqueStatForCampaignUrl($id, $type, $value)
    {
        switch ($type) {
            case AWeberReport::UNIQUE_OPENS:
                $this->reportRepo->updateStatCount($id, "unique_opens", $value);
                break;
            case AWeberReport::UNIQUE_CLICKS:
                $this->reportRepo->updateStatCount($id, "unique_clicks", $value);
                break;
            default:
                throw new JobException("Not a valid action type");
        }
    }

    public function splitTypes($processState)
    {
        return ['delivers','links'];
    }

    public function pushRecords(array $records, $targetId)
    {
    }

    public function getUniqueJobId(&$processState)
    {
        $jobId = (isset($processState['jobId']) ? $processState['jobId'] : '');

        if (isset($processState['campaign'])) {
            $jobId .= "::{$processState['campaign']->esp_internal_id}";
        }
        if (isset($processState['pageNumber'])) {
            $jobId .= "::{$processState['pageNumber']}::";
        }

        return $jobId;
    }

    public function savePage(&$processState, $forceWriteBool = false)
    {
        $count = 0;
        $shouldProcess = false;
        $espInternalId = $processState['campaign']->esp_internal_id;
        $deployId = $processState['campaign']->external_deploy_id;
        $key = "AWEBER.{$espInternalId}.{$processState['recordType']}";
        /**
         * Aweber will only let us page at 100 per.  that is causing to many new action events, so we are storing data in the cache.
         * Except opens which are done as a bulk group in a step.
         */
        if (!isset($processState['openCollection'])) { // opens are coming in as a collection so skip the cash.
            if ($forceWriteBool) { //We have a boolean set to save the extra rows after the main job finishes.
                $messages = Cache::get($key);
                $shouldProcess = true;
                Cache::forget($key);
            } else if (Cache::has($key)) { //while we are pagiging data
                $pastMessages = Cache::get($key);
                if (isset($processState['currentPageData']['response']['entries'])) {
                    $messages = array_merge($pastMessages, $processState['currentPageData']['response']['entries']);
                } else {
                    $messages = $pastMessages;  //most likely the last page
                }
            } else {  //first run
                $messages = $processState['currentPageData']['response']['entries'];
            }
            if (!$forceWriteBool){  // normal runs if
                if (count($messages) >= 10000) {
                    $shouldProcess = true;
                    Cache::forget($key);
                } else {
                    sleep(1);//crap rate limiting
                    Cache::put($key, $messages, 300);
                }
            }
        } else { //We have a open collection
            $shouldProcess = true;
            $messages = $processState['currentPageData'];
        }

        if(count($messages) == 0){
            return 0;
        }
        try {
            if ($shouldProcess) {

                switch ($processState['recordType']) {
                    case 'delivers' :
                        foreach ($messages as $message) {
                            $count++;
                            $emailAddress = AWeberEmailAction::getEmailAddressFromUrl($message['subscriber_link']);
                            if ($emailAddress) {  //we have the email
                                $this->emailRecord->queueDeliverable(
                                    self::RECORD_TYPE_DELIVERABLE,
                                    $emailAddress['email_address'],
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message['event_time']);
                            } else {
                                AWeberEmailAction::queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_DELIVERABLE,
                                    $message['subscriber_link'],
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message['event_time']);
                                //
                                //Everything that is missing passes through here, so maybe the email
                                // will be picked up before the other jobs occur.
                                //TODO MAKE THIS MORE THEN 1 EMAIL
                                //$this->dispatch((new UpdateSingleAWeberSubscriber($message['subscriber_link'], $this->api->getEspAccountId(), str_random(16)))->onQueue("AWeber"));
                            }
                            if ($message['total_opens'] > 0) {
                                $processState['openCollection'][] = $message['opens_collection_link'];
                                //collect end point urls
                            }
                        }
                        //if we have any opens lets clone the process change some goodies and move along.
                        if (count($processState['openCollection']) > 0) {
                            $newProcess = $processState;
                            $newProcess['espInternalId'] = $espInternalId;
                            $newProcess['pageNumber'] = 1;
                            $newProcess['recordType'] = 'opens';
                            $newProcess['currentFilterIndex']++;
                            $job = (new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $newProcess['recordType'], str_random(16), $newProcess))->onQueue("AWeber");
                            $this->dispatch($job);
                        }

                        unset($processState['openCollection']);//get rid of that we can continue paging. 
                        AWeberEmailAction::massRecordDeliverables();
                        $this->emailRecord->massRecordDeliverables();
                        break;

                    case 'opens' :
                        $count++;
                        foreach ($messages as $message) {  //magic is in the method to retrieve data
                            $emailAddress = AWeberEmailAction::getEmailAddressFromUrl($message['subscriber_link']);
                            if ($emailAddress) {
                                $this->emailRecord->queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_OPENER,
                                    $emailAddress->email_address,
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message['event_time']);
                            } else {
                                AWeberEmailAction::queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_OPENER,
                                    $message['subscriber_link'],
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message['event_time']);
                            }
                            
                        }
                        AWeberEmailAction::massRecordDeliverables();
                        $this->emailRecord->massRecordDeliverables();
                        break;

                    case 'links' :  //if a link has clicks lets start that process.
                        foreach ($messages as $message) {
                            if ($message['total_clicks'] > 0) {
                                $processState['clickCollection'] = $message['clicks_collection_link'];
                                $processState['pageNumber'] = 1;
                                $processState['recordType'] = 'clicks';
                                $job = (new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $processState['recordType'], str_random(16), $processState))->onQueue("AWeber");
                                $this->dispatch($job);
                            }
                            $processState['recordType'] = 'links';
                        }
                        break;

                    case 'clicks' :
                        foreach ($messages as $message) {
                            $count++;
                            $emailAddress = AWeberEmailAction::getEmailAddressFromUrl($message['subscriber_link']);
                            if ($emailAddress) {
                                $this->emailRecord->queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_CLICKER,
                                    $emailAddress['email_address'],
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message['event_time']);
                            } else {
                                AWeberEmailAction::queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_CLICKER,
                                    $message['subscriber_link'],
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message['event_time']);
                            }
                        }
                        AWeberEmailAction::massRecordDeliverables();
                        $this->emailRecord->massRecordDeliverables();
                        break;
                }
            }
        } catch (\Exception $e) {
            $jobException = new JobException('Failed to retrieve records. ' . $e->getMessage(), JobException::NOTICE);
            $jobException->setDelay(180);
            throw $jobException;
        }
        return $count;
    }

    public function generateOpenRecordData($processState)
    {
        $openCollections = $processState['openCollection'];
        $finalArray = array();
        $failedArray = array();
        foreach ($openCollections as $key => $openCollection) {
            try {
                echo $openCollection;
                $messages = $this->api->makeRawApiRequest($openCollection, array("ws.size" => 100), true);
                $finalArray = array_merge($finalArray, $messages['response']['entries']);
            } catch (\Exception $e) {
                $failedArray[] = $openCollection;
            }
        }
        //if we have any failures lets start a job with all of them. 
        if (count($failedArray) > 0) {
            $newProcess = $processState;
            $newProcess['openCollection'] = $failedArray;
            $newProcess['retryFailures']  = isset($newProcess['retryFailures']) ? $newProcess['retryFailures']++ : 1;
            $job = (new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $processState['recordType'], str_random(16), $newProcess))->onQueue("AWeber");
            $this->dispatch($job);
        }
        $this->currentPageData = $finalArray;
    }


    public function getBySubject($subject) {
        return $this->reportRepo->getBySubjectForFullDeploy($subject);
    }

    public function convertRawToStandard($request, $deploy)
    {
        $campaignName = $deploy->deploy_name;
        $internalId = $request->get('internal_id');

        $rawRecord = $this->reportRepo->getRowByExternalId($internalId);
        $rawRecord['campaign_name'] = $campaignName;
        $rawRecord->save();
        $rawRecord['deploy_id'] = $deploy->id;

        $standardRecord = $this->mapToStandardReport($rawRecord);
        $this->standardService->insertStandardStats($standardRecord);
    }

    public function mapToStandardReport($data)
    {
        return array(
            'campaign_name' => isset($data['campaign_name']) ? $data['campaign_name'] : "",
            'external_deploy_id' => isset($data['deploy_id']) ? $data['deploy_id'] : 0,
            'm_deploy_id' => 0,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $data['internal_id'],
            'datetime' => $data['datetime'],
            'name' => "",
            'subject' => $data['subject'],
            'from' => "",
            'from_email' => "",
            'delivered' => $data['total_sent'],
            'bounced' => "",
            'e_opens' => $data['total_opens'],
            'e_opens_unique' => "",
            'e_clicks' => $data['total_clicks'],
            'e_clicks_unique' => "",
        );
    }

    public function getPageData()
    {
        return $this->currentPageData;
    }

    public function pageHasCampaignData($processState)
    {
        $report = $this->getRawReportByInternalId($processState['campaign']->esp_internal_id);

        if (isset($processState['openCollection'])) {
            $statUrl = $processState['openCollection'];
        } else if (isset($processState['clickCollection'])) {
            $statUrl = $processState['clickCollection'];
        } else if ($processState['recordType'] == 'links') {
            $statUrl = "{$report->info_url}/links";
        } else {
            $statUrl = "{$report->info_url}/messages";
        }

        if ($this->getStartNumber() == 0) {
            $messages = $this->api->makeRawApiRequest($statUrl, array("ws.size" => 100), true);
        } else {
            $messages = $this->api->makeRawApiRequest($statUrl, array("ws.size" => 100, "ws.start" => $this->getStartNumber()), true);
        }

        $this->currentPageData = $messages;
        return !empty($messages['response']['entries']);
    }

    public function getRawReportByInternalId($internalId)
    {
        return $this->reportRepo->getRowByExternalId($internalId);
    }

    public function getStartNumber()
    {
        return ($this->pageNumber - 1) * 100;
    }

    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    public function nextPage()
    {
        return $this->pageNumber++;
    }

    public function setPageType($pageType)
    {
        $this->pageType = $pageType;
    }


}
