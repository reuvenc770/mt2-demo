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

    public function splitTypes()
    {
        return ['open','click','bounce','unsubscribe'];
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
                        $deployId = isset($map[$espInternalId]) ?
                            (int)$map[$espInternalId] : 0;
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
                        $deployId = isset($map[$espInternalId]) ? (int)$map[$espInternalId] : 0;
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
            "start" => Carbon::now()->subDay(15)->toAtomString(), //TODO NOT SURE HOW TO GET DATE HERE WELL, HARDCODING TILL WE NEED TO BE DYNAMIC
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
        return $id;
    }
}