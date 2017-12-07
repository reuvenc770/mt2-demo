<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\DeployActionEntry;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use App\Services\API\InfusionSoftApi;
use App\Services\EmailRecordService;
use App\Services\Interfaces\IDataService;
use Illuminate\Support\Facades\Event;


/**
 * Class InfusionSoftReportService
 * @package App\Services
 */
class InfusionSoftReportService extends AbstractReportService implements IDataService
{
    /**
     * InfusionSoftReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */

    const DELIVERABLE_LOOKBACK = 2;

    /**
     * InfusionSoftReportService constructor.
     * @param ReportRepo $reportRepo
     * @param InfusionSoftApi $api
     * @param EmailRecordService $emailRecord
     */
    public function __construct(ReportRepo $reportRepo, InfusionSoftApi $api, EmailRecordService $emailRecord)
    {
        parent::__construct($reportRepo, $api, $emailRecord);
    }

    /**
     * @param $date
     * @return array
     * @throws \Exception
     */
    public function retrieveApiStats($date)
    {
        return $this->api->sendApiRequest();
    }

    /**
     * @param $data
     */
    public function insertApiRawStats($data)
    {
        $reportList = [];
        $espAccountId = $this->api->getEspAccountId();

        foreach($data as $row) {
            $reportList[] = $this->mapToRawReport($row, $espAccountId);
        }

        Event::fire(new RawReportDataWasInserted($this, $reportList));
    }

    /**
     * @param $report
     * @return mixed
     */
    public function mapToStandardReport($report)
    {
        return [
            'campaign_name' => $report['name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $report['esp_account_id'],
            'esp_internal_id' => $report['internal_id'],
            'datetime' => $report['published_datetime'],
            'subject' => '',
            'from' => '',
            'from_email' => '',
            'e_sent' => $report['completed_contact_count'],
            'delivered' => $report['active_contact_count'],
            'bounced' => 0,
            'optouts' => 0,
            'e_opens' => 0,
            'e_opens_unique' => 0,
            'e_clicks' => 0,
            'e_clicks_unique' => 0,
        ];
    }

    /**
     * @param $report
     * @return mixed
     */
    public function mapToRawReport($report, $espAccountId)
    {
        return [
            'internal_id' => $report[''],
            'esp_account_id' => $espAccountId,
            'name' => $report[''],
            'datetime_created' => $report[''],
            'time_zone' => $report[''],
            'published_datetime' => $report[''],
            'published_time_zone' => $report[''],
            'published_status' => $report[''],
            'active_contact_count' => $report[''],
            'completed_contact_count' => $report[''],
            'error_message' => $report['']
        ];
    }

    /**
     * @param $processState
     */
    public function getUniqueJobId(&$processState)
    {
        // This is how the job  will be labeled  in the job_entries table
    }

    /**
     * @param $processState
     */
    public function getTypeList(&$processState)
    {
        //TODO Remove if unneeded
    }

    /**
     * @param $espAccountId
     * @param $campaign
     * @param $recordType
     * @param bool $isRerun
     * @throws JobException
     */
    public function startTicket($espAccountId, $campaign, $recordType, $isRerun = false)
    {
        try {
            //TODO Remove if unneeded
        } catch (\Exception $e) {
            $jobException = new JobException('Failed to start report ticket. ' . $e->getMessage(), JobException::NOTICE, $e);
            $jobException->setDelay(180);
            throw $jobException;
        }
    }

    /**
     * @param $processState
     * @throws JobException
     */
    public function downloadTicketFile(&$processState)
    {
        try {
            //TODO Remove if unneeded
        } catch (\Exception $e) {
            $jobException = new JobException('Failed to download report ticket. ' . $e->getMessage(), JobException::NOTICE, $e);
            $jobException->setDelay(180);
            throw $jobException;
        }
    }

    /**
     * @param $processState
     * @return int
     * @throws JobException
     */
    public function saveRecords(&$processState)
    {
        $count = 0;
        try {
            //TODO Remove if unneeded
            //RETURN ROW COUNT OF SAVES
        } catch (\Exception $e) {
            $exceptionType = get_class($e);
            DeployActionEntry::recordFailedRun($this->api->getEspAccountId(), $processState['campaign']->esp_internal_id, $processState['recordType']);
            $jobException = new JobException("Failed to process report file - $exceptionType: " . $e->getMessage(), JobException::WARNING, $e);
            $jobException->setDelay(60);
            throw $jobException;
        }
        DeployActionEntry::recordSuccessRun($this->api->getEspAccountId(), $processState['campaign']->esp_internal_id, $processState['recordType']);
        return $count;
    }


    /**
     * @param $processState
     */
    public function cleanUp($processState)
    {
        //TODO Remove if unneeded
    }
}
