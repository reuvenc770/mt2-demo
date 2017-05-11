<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/9/16
 * Time: 11:21 AM
 */

namespace App\Services;

use App\Facades\BrontoMapping;
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
use Log;

class BrontoReportService extends AbstractReportService implements IDataService
{
    public $pageNumber = 1;

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
            'status' => 'sent'
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

    public function splitTypes($processState)
    {
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
        if (isset($processState['recordType']) && 'delivered' === $processState['recordType']) {
            return ['delivered', 'bounce', 'unsubscribe'];
        } else {
            return ['open', 'click'];
        }
    }

    public function saveActionPage($processState, $map)
    {
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

            case 'bounce' :
                $type = false;
                $deployActionType = "bounce";
                break;

            case 'unsubscribe' :
                $type = false;
                $deployActionType = "unsubscribe";
                break;

            default:
                throw new \Exception("Inappropriate type record type {$processState['recordType']} in saveActionPage"); // THIS SHOULD BE SOMETHING ELSE
        }

        try {
            if ($type) {
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
            } else {
                foreach ($processState['currentPageData'] as $bounce) {
                    $espInternalId = $this->parseInternalId($bounce->getDeliveryId());
                    if ("bounce" == $deployActionType) {
                        Suppression::recordRawHardBounce(
                            $this->api->getId(),
                            $bounce->getEmailAddress(),
                            $espInternalId,
                            $bounce->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                    } else {
                        Suppression::recordRawUnsub(
                            $this->api->getId(),
                            $bounce->getEmailAddress(),
                            $espInternalId,
                            $bounce->getCreatedDate()->format('Y-m-d H:i:s')
                        );
                    }
                    $internalIds[] = $espInternalId;
                }

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
                case 'open' :
                    foreach ($processState['currentPageData'] as $opener) {
                        $espInternalId = $this->parseInternalId($opener->getDeliveryId());
                        $deployId = $this->getDeployIdFromCampaignName($opener->getMessageName());
                        
                        if ( empty( $deployId ) ) {
                                continue;
                        }
                        
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
                        
                        if ( empty( $deployId ) ) {
                                continue;
                        }
                        
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

    public function pageHasData( $processState )
    {

        if ($this->pageNumber != 1) {
            return false;
        }
        $filter = array(
            "start" => Carbon::parse( $processState[ 'date' ] )->toAtomString() ,
            "deliveryId" => $processState[ 'campaign' ]->internal_id ,
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
        $jobId = isset($processState['campaign']) ? ":!:{$processState['campaign']['external_deploy_id']} - {$processState['campaign']['esp_internal_id']}" : '';
        return $jobId;
    }

    //
    public function mapToStandardReport($data)
    {
        $deployId = $this->parseSubID($data['message_name']);
        $espInternalId = $this->parseInternalId($data['internal_id']);

        return array(
            'campaign_name' => $data['message_name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $espInternalId,
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

    //I am not sure how to make this generic could use regex maybe. 
    public function parseInternalId($id)
    {
        $pos = 0;
        if (strrpos($id, '0001')) {
            $pos = strrpos($id, '0001');
            $hexId = substr($id, $pos + 3);
            $id = base_convert($hexId, 16, 10);
        } elseif (strrpos($id, '0002')) {
            $pos = strrpos($id, '0002');
            $hexId = substr($id, $pos + 3);
            $id = base_convert($hexId, 16, 10);
        } else {
            $id = BrontoMapping::returnOrGenerateID($id, $this->api->getId());
        }
        if (empty($id)) {
            throw new JobException("ID cannot be parsed");
        } else {
            return $id;
        }

    }

    public function pageHasCampaignData($processState)
    {

        $formattedInternalId = $processState['campaign']->esp_internal_id;
        $type = $processState['recordType'];
        $datetime = $processState['campaign']->datetime;
        $pipe = $processState['pipe'];

        if ($this->pageNumber != 1) {
            return false;
        }

        $filter = array(
            "start" => Carbon::parse($datetime)->toAtomString(),
            "size" => "5000",
            "types" => $type,
            "deliveryId" => $formattedInternalId,
            "readDirection" => $this->getPageNumber(),
        );

        if ('rerun' === $pipe) {
            $filter['start'] = Carbon::parse($datetime)->subDay(1)->toAtomString(); // 
        }

        if ($type == "delivered") {
            $filter['types'] = "send";
            $data = $this->api->getOutgoingSends($filter);
        } else {
            $data = $this->api->getDeliverablesByType($filter);
        }
        $this->currentPageData = $data;
        return true;
    }

    public function pullUnsubsEmailsByLookback($date)
    {
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
            if ($entry->getDeliveryId()) {
                $espInternalId = $this->parseInternalId($entry->getDeliveryId());
                Suppression::recordRawUnsub($espAccountId, $entry->getEmailAddress(), $espInternalId, $entry->getCreatedDate()->format('Y-m-d H:i:s'));
            }
            else {
                Log::info($entry->getDeliveryId() . ', ' . $entry->getEmailAddress() . ' not found for Bronto');
            }
            
        }
    }

    protected function getDeployIdFromCampaignName($campaignName)
    {
        return strstr($campaignName, '_', true);
    }

    public function pushRecords(array $records, $targetId)
    {
        foreach ($records as $record) {
            $result = $this->api->addContact($record);
        }
    }

    public function addContactToLists($emailAddress, $lists)
    {
        $contactInfo = [
            'email' => $emailAddress,
            'listIds' => $lists
        ];
        $this->api->addContact($contactInfo);
    }


    public function getRawReportsForSplit($campaignName, $epsAccountId)
    {
        return $this->reportRepo->getRawCampaignsFromName($campaignName, $epsAccountId);
    }

    public function getRawCampaigns ( $processState ) {
        return $this->reportRepo->getRawCampaignsFromDate( $processState[ 'date' ] );
    }
}
