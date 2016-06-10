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
class BrontoReportService extends AbstractReportService implements IDataService
{
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
        //Event::fire(new RawReportDataWasInserted($this, $arrayReportList));
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
        return ['opens', 'clicks', 'unsubscribes', 'bounces'];
    }

    public function savePage(&$processState, $map)
    {
        $type = "";
        $internalIds = array();
        try {
            switch ($processState['recordType']) {
                case 'opens' :
                    foreach ($processState['currentPageData'] as $key => $opener) {
                        $deployId = isset($map[$opener['campaign_id']]) ? (int)$map[$opener['campaign_id']] : 0;
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_OPENER,
                            $opener['contact']['email'],
                            $this->api->getId(),
                            $deployId,
                            $opener['campaign_id'],
                            $opener['recorded_at']
                        );
                        $internalIds[] = $opener['campaign_id'];
                    }
                    $type = "open";
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

    public function mapToStandardReport($data)
    {
        // TODO: Implement mapToStandardReport() method.
    }
}