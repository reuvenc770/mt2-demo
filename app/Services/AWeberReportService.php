<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\AWeberEmailAction;
use App\Facades\DeployActionEntry;
use App\Jobs\RetrieveDeliverableReports;
use App\Jobs\UpdateSingleAWeberSubscriber;
use App\Models\AWeberList;
use App\Models\AWeberReport;
use App\Repositories\AWeberListRepo;
use App\Repositories\ReportRepo;
use App\Services\API\AWeberApi;
use App\Services\Interfaces\IDataService;
use App\Services\StandardReportService;
use App\Factories\APIFactory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;
use Cache;

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
        return ['delivers', 'links'];
    }


    public function pushRecords(array $records, $targetId)
    {
    }

    public function getUniqueJobId(&$processState)
    {
        $jobId = (isset($processState['jobId']) ? $processState['jobId'] : '');

        if (
            !isset($processState['jobIdIndex'])
            || (isset($processState['jobIdIndex']) && $processState['jobIdIndex'] != $processState['currentFilterIndex'])
        ) {
            switch ($processState['currentFilterIndex']) {
                case 1 :
                    $jobId .= "::{$processState['espAccountId']}";
                    break;
            }

            if (isset($processState['espInternalId'])) {
                $jobId .= "::{$processState['espInternalId']}::{$this->pageNumber}::";
            }


            $processState['jobIdIndex'] = $processState['currentFilterIndex'];
            $processState['jobId'] = $jobId;
        }
        return $jobId;
    }


    public function savePage(&$processState, $forceWriteBool = false)
    {

        $count = 0;
        $shouldProcess = false;
        $espInternalId = $processState['campaign']->esp_internal_id;
        $deployId = $processState['campaign']->external_deploy_id;

        /**
         * Aweber will only let us page at 100 per.  that is causing to many new action events, so we are storing data in the cache.
         */
        $key = "AWEBER.{$espInternalId}.{$processState['recordType']}";
        if ($forceWriteBool) { //last page saved alone.
            $messages = Cache::get($key);
            $shouldProcess = true;
            Cache::forget($key);
        }
        else if (Cache::has($key)) { //during paging.
            $pastMessages = Cache::get($key);
            $messages = array_merge($pastMessages, $processState['currentPageData']['response']['entries']);
        } else //first run
        {
            $messages = $processState['currentPageData']['response']['entries'];
        }

        if (!$forceWriteBool) {
            if (count($messages) >= 10000) {
                $shouldProcess = true;
                Cache::forget($key);
            } else {
                sleep(1);//crap rate limiting
                Cache::put($key, $messages, 300);
            }
        }

        try {
            if ($shouldProcess) {//this could be refactored
                switch ($processState['recordType']) {
                    case 'delivers' :
                        foreach ($messages as $message) {
                            $emailAddress = AWeberEmailAction::getEmailAddressFromUrl($message['subscriber_link']);
                            if ($emailAddress) {
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
                                //will be picked up before the other jobs occur.
                                // $this->dispatch((new UpdateSingleAWeberSubscriber($message['subscriber_link'], $this->api->getEspAccountId(), str_random(16)))->onQueue("AWeber"));
                            }
                            if ($message['total_opens'] > 0) {
                                $processState['openCollection'] = $message['opens_collection_link'];
                                $processState['espInternalId'] = $espInternalId;
                                $processState['pageNumber'] = 1;
                                $processState['recordType'] = 'opens';
                                $job = (new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $processState['recordType'], $message['id'], $processState))->onQueue("AWeber");
                                $this->dispatch($job);
                            }
                            unset($processState['openCollection']);
                            $processState['recordType'] = 'delivers';
                            $count++;
                        }

                        AWeberEmailAction::massRecordDeliverables();
                        $this->emailRecord->massRecordDeliverables();
                        break;

                    case 'opens' :
                        foreach ($messages as $message) {

                            $emailAddress = AWeberEmailAction::getEmailAddressFromUrl($message->subscriber_link);

                            if ($emailAddress) {
                                $this->emailRecord->queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_OPENER,
                                    $emailAddress->email_address,
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message->event_time);
                            } else {
                                AWeberEmailAction::queueDeliverable(
                                    AbstractReportService::RECORD_TYPE_OPENER,
                                    $message->subscriber_link,
                                    $this->api->getEspAccountId(),
                                    $deployId,
                                    $espInternalId,
                                    $message->event_time);
                            }
                            $count++;
                        }
                        AWeberEmailAction::massRecordDeliverables();
                        $this->emailRecord->massRecordDeliverables();
                        break;

                    case 'links' :
                        foreach ($messages as $message) {
                            if ($message['total_clicks'] > 0) {
                                $processState['clickCollection'] = $message['clicks_collection_link'];
                                $processState['recordType'] = 'clicks';
                                $job = (new RetrieveDeliverableReports("AWeber", $this->api->getEspAccountId(), $processState['recordType'], str_random(16), $processState))->onQueue("AWeber");
                                $this->dispatch($job);
                            }
                            $processState['recordType'] = 'links';
                        }
                        break;

                    case 'clicks' :
                        foreach ($messages as $message) {
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
                            $count++;
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

    public function getRawReportByInternalId($internalId)
    {
        return $this->reportRepo->getRowByExternalId($internalId);
    }


    public function getBySubject($subject)
    {
        return $this->reportRepo->getBySubject($subject);
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

    public function getPageData()
    {
        return $this->currentPageData;
    }

    public function pageHasCampaignData($processState)
    {
        $report = $this->getRawReportByInternalId($processState['campaign']->esp_internal_id);

        if (isset($processState['openCollection'])) {
            $statUrl = $processState['openCollection'];
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

    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    public function getStartNumber()
    {
        return ($this->pageNumber - 1) * 100;
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
