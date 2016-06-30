<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/9/16
 * Time: 11:21 AM
 */

namespace App\Services;

use App\Services\AbstractReportService;
use App\Repositories\ReportRepo;
use App\Services\API\BrontoApi;
use App\Services\Interfaces\IDataService;
use App\Services\EmailRecordService;
use Carbon\Carbon;
use App\Exceptions\JobException;
use App\Facades\DeployActionEntry;
use App\Facades\Suppression;
use App\Events\RawReportDataWasInserted;
use Event;

class BrontoReportService extends AbstractReportService implements IDataService
{
    public $pageNumber = 1;
    CONST DUMB_ID = "0bce03ee";
    public $pageType;
    public $currentPageData = array();

    public function __construct(ReportRepo $reportRepo, BrontoApi $api, EmailRecordService $emailRecord)
    {
        parent::__construct($reportRepo, $api, $emailRecord);
    }

    public function retrieveApiStats($data)
    {
        $filter = array('start' =>
            array('operator' => 'After', 'value' => $data),
            'status' => 'sent',
            'deliveryType' => "normal"
        );

        return $this->api->getCampaigns($filter);
    }

    public function insertApiRawStats($data)
    {
        $arrayReportList = array();
        $espAccountId = $this->api->getEspAccountId();
        foreach ($data as $report) {
            $convertedReport = $this->mapToRawReport($report);
            $this->insertStats($espAccountId, $convertedReport);
            $arrayReportList[] = $convertedReport;
        }
        Event::fire(new RawReportDataWasInserted($this, $arrayReportList));
    }

    public function mapToRawReport($data)
    {
        $return = array();
        $return["internal_id"] = $data->id;
        $return["esp_account_id"] = $this->api->getEspAccountId();
        foreach ($data->toArray() as $key => $field) {
            $return[snake_case($key)] = $field;
        }
        $return['message_name'] = $data->messageName;
        return $return;
    }

    public function splitTypes($processState) {
        if ('rerun' === $processState['pipe']) {
            $typeList = [];
            // data in $processState['campaign']

            if (1 == $processState['campaign']->delivers) {
                $typeList[] = "delivered";
            }
            if (1 == $processState['campaign']->opens) {
                $typeList[] = 'open';
            }
            if (1 == $processState['campaign']->clicks) {
                $typeList[] = 'click';
            }
            if (1 == $processState['campaign']->unsubs) {
                $typeList[] = 'unsub';
            }
            if (1 == $processState['campaign']->bounces) {
                $typeList[] = 'bounce';
            }
            return $typeList;
        }
        else {
            return ['open','click','bounce','unsubscribe'];
        }
        
    }

    public function saveActionPage($processState, $map) {
        $type = "";
        $internalIds = array();

        switch ($processState['recordType']) {

            case 'delivered':
                $type = self::RECORD_TYPE_DELIVERABLE;
                $deployActionType = 'deliverable';
                break;

            case 'open':
                $type = self::RECORD_TYPE_OPENER;
                $deployActionType = 'open';
                break;

            case 'click':
                $type = self::RECORD_TYPE_CLICKER;
                $deployActionType = 'click';
                break;

	    //TODO - bounces, unsubs
            default:
                throw new \Exception("Inappropriate type record type {$processState['recordType']} in saveActionPage"); // THIS SHOULD BE SOMETHING ELSE
        }

        try {
            foreach ($processState['currentPageData'] as $key => $record) {

                $deployId = $this->getDeployIdFromCampaignName($record->getMessageName());
                $this->emailRecord->queueDeliverable(
                    $type,
                    $record->getEmailAddress(),
                    $this->api->getId(),
                    $deployId,
                    $this->parseInternalId($record->getDeliveryId()), // esp_internal_id
                    $record->getCreatedDate()->format('Y-m-d H:i:s')
                );
                $internalIds[] = $this->parseInternalId($record->getDeliveryId());
            }
        } catch (\Exception $e) {
            DeployActionEntry::recordFailedRunArray($this->api->getEspAccountId(), array_unique($internalIds), $deployActionType);
            $jobException = new JobException('Failed to retrieve records. ' . $e->getMessage(), JobException::NOTICE);
            $jobException->setDelay(180);
            throw $jobException;
        }
        $this->emailRecord->massRecordDeliverables();
        DeployActionEntry::recordSuccessRunArray($this->api->getEspAccountId(), array_unique($internalIds), $deployActionType);
    }

    public function savePage(&$processState, $map)
    {
        $type = "";
        $internalIds = array();
        try {
            switch ($processState['recordType']) {

                case 'delivered' :
                    foreach ($processState['currentPageData'] as $opener) {
                        $espInternalId = $this->parseInternalId($opener->getDeliveryId());
                        $deployId = $this->getDeployIdFromCampaignName($opener->getMessageName());

                        if ($opener->getDeliveryType() != 'bulk') {
                            continue;
                        }
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_DELIVERABLE,
                            $opener->getEmailAddress(),
                            $this->api->getId(),
                            $deployId,
                            $espInternalId,
                            $opener->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                        $internalIds[] = $espInternalId;
                    }
                    $type = "deliverable";
                    break;
                case 'open' :
                    foreach ($processState['currentPageData'] as $opener) {
                        $espInternalId = $this->parseInternalId($opener->getDeliveryId());
                        $deployId = $this->getDeployIdFromCampaignName($opener->getMessageName());
                        
                        if ($opener->getDeliveryType() != 'bulk') {
                            continue;
                        }
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_OPENER,
                            $opener->getEmailAddress(),
                            $this->api->getId(),
                            $deployId,
                            $espInternalId,
                            $opener->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                        $internalIds[] = $espInternalId;
                    }
                    $type = "open";
                    break;
                case 'click' :
                    foreach ($processState['currentPageData'] as $opener) {
                        if ($opener->getDeliveryType() != 'bulk') {
                            continue;
                        }
                        $espInternalId = $this->parseInternalId($opener->getDeliveryId());
                        $deployId = $this->getDeployIdFromCampaignName($opener->getMessageName());
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_CLICKER,
                            $opener->getEmailAddress(),
                            $this->api->getId(),
                            $deployId,
                            $espInternalId,
                            $opener->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                        $internalIds[] = $espInternalId;
                    }
                    $type = "click";
                    break;
                case 'bounce' :
                    foreach ($processState['currentPageData'] as $bounce) {
                        $espInternalId = $this->parseInternalId($bounce->getDeliveryId());
                        Suppression::recordRawHardBounce(
                            $this->api->getId(),
                            $bounce->getEmailAddress(),
                            $espInternalId,
                            "",
                            $bounce->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                        $internalIds[] = $espInternalId;
                    }
                    $type = "optout";
                    break;

                case 'unsubscribe' :
                    foreach ($processState['currentPageData'] as $bounce) {
                        $espInternalId = $this->parseInternalId($bounce->getDeliveryId());
                        Suppression::recordRawUnsub(
                            $this->api->getId(),
                            $bounce->getEmailAddress(),
                            $espInternalId,
                            '',
                            $bounce->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                        $internalIds[] = $espInternalId;
                    }
                    $type = "bounce";
                    break;
            }
        } catch (\Exception $e) {
            DeployActionEntry::recordFailedRunArray($this->api->getEspAccountId(), array_unique($internalIds), $type);
            $jobException = new JobException('Failed to retrieve records. ' . $e->getMessage(), JobException::NOTICE);
            $jobException->setDelay(180);
            throw $jobException;
        }
        $this->emailRecord->massRecordDeliverables();
        DeployActionEntry::recordSuccessRunArray($this->api->getEspAccountId(), array_unique($internalIds), $type);
    }


    public function getPageData()
    {
        return $this->currentPageData;
    }

    public function pageHasData()
    {

        if ($this->pageNumber != 1) {
            return false;
        }
        $filter = array(
            "start" => Carbon::now()->subDay(3)->toAtomString(), //TODO NOT SURE HOW TO GET DATE HERE WELL, HARDCODING TILL WE NEED TO BE DYNAMIC
            "size" => "5000",
            "types" => $this->pageType,
            "readDirection" => $this->getPageNumber(),
        );
        $data = $this->api->getDeliverablesByType($filter);
        $this->currentPageData = $data;


        return true;
    }

    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    public function getPageNumber()
    {
        if ($this->pageNumber == 1) {
            return "FIRST";
        } else {
            return "NEXT";
        }
    }

    public function nextPage()
    {
        return $this->pageNumber++;
    }


    public function setPageType($pageType)
    {
        if (in_array($pageType, ['open', 'click', 'unsubscribe', 'bounce'])) {
            $this->pageType = $pageType;
        }
    }


    public function getUniqueJobId(&$processState)
    {
        $jobId = (isset($processState['jobId']) ? $processState['jobId'] : '');

        if (
            !isset($processState['jobIdIndex'])
            || (isset($processState['jobIdIndex']) && $processState['jobIdIndex'] != $processState['currentFilterIndex'])
        ) {
            $filterIndex = $processState['currentFilterIndex'];
            $pipe = $processState['pipe'];

            if ($pipe == 'default' && $filterIndex == 1) {
                $jobId .= '::Pipe-' . $pipe . '::' . $processState['recordType'] . '::Page-' . (isset($processState['pageNumber']) ? $processState['pageNumber'] : 1);
            } elseif ($pipe == 'delivered' && $filterIndex == 1) {
                $jobId .= (isset($processState['campaign']) ? '::Pipe-' . $pipe . '::Campaign-' . $processState['campaign']->esp_internal_id : '');
            }

            $processState['jobIdIndex'] = $processState['currentFilterIndex'];
            $processState['jobId'] = $jobId;
        }

        return $jobId;
    }

    public function mapToStandardReport($data)
    {
        $deployId = $this->parseSubID($data['message_name']);
        return array(
            'campaign_name' => $data['message_name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $this->parseInternalId($data['internal_id']),
            'datetime' => $data['start'],
            //'subject' => $data['subject'],
            'from' => $data['from_name'],
            'from_email' => $data['from_email'],
            'e_sent' => $data['num_sends'],
            'delivered' => $data['num_deliveries'],
            'bounced' => (int)$data['num_bounces'],
            #'optouts' => $data[''],
            'e_opens' => $data['num_opens'],
            'e_opens_unique' => $data['uniq_opens'],
            'e_clicks' => $data['num_clicks'],
            'e_clicks_unique' => $data['uniq_clicks'],
        );
    }

    public function parseInternalId($id)
    {
        $pos = strrpos($id, '0001');
        $hexId = substr($id, $pos + 3);
        $id = base_convert($hexId, 16, 10);
        if ($id > 10) {  //not a mailer
           $id = 0;
        }
        return $id;
    }

    public function makeDumbInternalId($id){
        $converted = base_convert($id,10,16);
        $padded = str_pad($converted,28,'0',STR_PAD_LEFT);
        return self::DUMB_ID.$padded;
    }

    public function pageHasCampaignData($processState){

        $formattedInternalId = $processState['campaign']->esp_internal_id;
        $type = $processState['recordType'];
        $datetime = $processState['campaign']->datetime;
        $pipe = $processState['pipe'];


        $realID = 'rerun' === $pipe ? $formattedInternalId : $this->makeDumbInternalId($formattedInternalId);
        if ($this->pageNumber != 1) {
            return false;
        }

        $filter = array(
            "start" => Carbon::parse($datetime)->toAtomString(),
            "size" => "5000",
            "types" => $type,
            "deliveryId" => $realID,
            "readDirection" => $this->getPageNumber(),
        );

        if ('rerun' === $pipe) {
            $filter['start'] = Carbon::parse($datetime)->subDay(1)->toAtomString(); // 
        }

        if($type == "delivered"){
            $filter['types'] = "send";
            $data = $this->api->getOutgoingSends($filter);
        } else {
            $data = $this->api->getDeliverablesByType($filter);
        }
        $this->currentPageData = $data;


        return true;
    }

    public function pullUnsubsEmailsByLookback($date){
        $filter = array(
            "start" => Carbon::now()->subDay($date)->toAtomString(),
            "size" => "5000",
            "types" => "unsubscribe",
            "readDirection" => "FIRST",
        );
       return $this->api->getDeliverablesByType($filter);
    }

    public function insertUnsubs($data, $espAccountId)
    {
        foreach ($data as $entry) {
            $espInternalId = $this->parseInternalId($entry->getDeliveryId());
            Suppression::recordRawUnsub($espAccountId, $entry->getEmailAddress(), $espInternalId, "", $entry->getCreatedDate()->format('Y-m-d H:i:s'));
        }
    }

    protected function getDeployIdFromCampaignName($campaignName) {
        return strstr($campaignName, '_', true);
    }
}
