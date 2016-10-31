<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\DeployActionEntry;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use App\Services\API\AWeberApi;
use App\Services\EmailRecordService;
use App\Services\Interfaces\IDataService;
use Illuminate\Support\Facades\Event;
use Log;

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

    const DELIVERABLE_LOOKBACK = 2;

    /**
     * AWeberReportService constructor.
     * @param ReportRepo $reportRepo
     * @param AWeberApi $api
     * @param EmailRecordService $emailRecord
     */
    public function __construct(ReportRepo $reportRepo, AWeberApi $api, EmailRecordService $emailRecord)
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
        $startTime = microtime( true );

        Log::info( 'Retrieving API Campaign Stats.......' );

        $date = null; //unfortunately date does not matter here.
        $numberToPull = 30; //lets get the last 20 campaigns sent
        $campaignData = array();
        $campaigns = $this->api->getCampaigns(1);
        $i=0;
        foreach ($campaigns as $campaign) {
            Log::info($campaign);
            Log::info( 'Processing Aweber Campaign ' . $campaign->id );

            $clickEmail =$this->api->getStateValue($campaign->id, "unique_clicks");
            $openEmail = $this->api->getStateValue($campaign->id, "unique_opens");
            $row = array(
                "internal_id" => $campaign->id,
                "subject" => $campaign->subject,
                "sent_at" => $campaign->sent_at,
                "info_url" => $campaign->self_link,
                "total_sent" => $campaign->total_sent,
                "total_opens" => $campaign->total_opens,
                "total_unsubscribes" => $campaign->total_unsubscribes,
                "total_clicks" => $campaign->total_clicks,
                "total_undelivered" => $campaign->total_undelivered,
                "unique_clicks" => $clickEmail,
                "unique_opens" => $openEmail,
            );
            $campaignData[] = $row;


            $i++;
            if($i == 20){
                $endTime = microtime( true );

                Log::info( 'Executed in: ' );
                Log::info(  $endTime - $startTime );
                return  $campaignData;
            }

        }
        $endTime = microtime( true );

        Log::info( 'Executed in: ' );
        Log::info(  $endTime - $startTime );

        return $campaignData;
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
            'campaign_name' => $data['campaign_name'],
            'external_deploy_id' => $this->getDeployIDFromName($data['campaign_name']),
            'm_deploy_id' => $this->getDeployIDFromName($data['campaign_name']),
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => "",
            'datetime' => $data['datetime'],
            'name' => "",
            'subject' => "",
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
    public function getTicketForMessageSubscriberData($messageId, $recordType, $isRerun)
    {
        //TODO Remove if unneeded
    }

    /**
     * @param $processState
     */
    public function checkTicketStatus(&$processState)
    {
        //TODO Remove if unneeded
    }

    /**
     * @param $processState
     */
    public function splitTypes($processState)
    {
        //TODO Remove if unneeded
    }

    /**
     * @param $pageType
     */
    public function setPageType($pageType)
    {
        //TODO Remove if unneeded
    }


    public function pushRecords(array $records, $targetId) {}

}
