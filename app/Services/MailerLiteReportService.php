<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\DeployActionEntry;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use App\Services\API\MailerLiteApi;
use App\Services\EmailRecordService;
use App\Services\Interfaces\IDataService;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;


/**
 * Class MailerLiteReportService
 * @package App\Services
 */
class MailerLiteReportService extends AbstractReportService implements IDataService
{
    /**
     * MailerLiteReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */

    const DELIVERABLE_LOOKBACK = 2;

    /**
     * MailerLiteReportService constructor.
     * @param ReportRepo $reportRepo
     * @param MailerLiteApi $api
     * @param EmailRecordService $emailRecord
     */
    public function __construct(ReportRepo $reportRepo, MailerLiteApi $api, EmailRecordService $emailRecord)
    {
        parent::__construct($reportRepo, $api, $emailRecord);
    }

    /**
     * @param $date
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function retrieveApiStats($date)
    {
        $this->api->setDate($date);
        return $this->api->sendApiRequest();
    }

    /**
     * @param $xmlData
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
        /**
            WE NEED DEPLOY ID SOMEHOW
        */
        $deployId = 0;
        return [
            'campaign_name' => $report['name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $report['esp_account_id'],
            'esp_internal_id' => $report['internal_id'],
            'datetime' => $report['datetime_send'],
            'subject' => $report['name'],
            'from' => '',
            'from_email' => '',
            'e_sent' => $report['total_recipients'],
            'delivered' => $report['total_recipients'],
            'bounced' => 0,
            'optouts' => 0,
            'e_opens' => $report['open'],
            'e_opens_unique' => $report['unique_opens'],
            'e_clicks' => $report['click'],
            'e_clicks_unique' => $report['unique_clicks'],
        ];
    }

    /**
     * @param $report
     * @return mixed
     */
    public function mapToRawReport($report, $espAccountId)
    {
        return [
            'internal_id' => $report->id,
            'name' => $report->name,
            'type' => $report->type,
            'total_recipients' => $report->total_recipients,
            'datetime_created' => $record->date_created,
            'datetime_send' => $record->date_send,
            'status' => $record->status,
            'opened' => $record->opened->count,
            'clicked' => $record->clicked->count,
            'esp_account_id' => $espAccountId
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

    /**
     * @param $messageId
     * @param $recordType
     * @param $isRerun
     */
}
