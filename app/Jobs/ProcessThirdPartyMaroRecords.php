<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\API\MaroApi;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Exceptions\JobCompletedException;
use Log;
use DB;
use App\Exceptions\JobAlreadyQueuedException;
use App\Exceptions\JobException;
use App\Models\JobEntry;

class ProcessThirdPartyMaroRecords extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;
    private $tracking;
    private $companyName;
    private $espAccountId;
    private $jobName;
    private $api;
    private $state;

    protected $logTypeMap = [
        JobException::NOTICE => 'notice',
        JobException::WARNING => 'warning',
        JobException::ERROR => 'error',
        JobException::CRITICAL => 'critical'
    ];
    protected $steps = [
        "splitTypes",
        "savePagininatedRecords",
        "pickCampaignsForSplit",
        "savePaginatedDeliveredRecords",
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyName, $espAccountId, $tracking, $state = null)
    {
        $this->companyName = $companyName;
        $this->espAccountId = $espAccountId;

        $this->tracking = $tracking;
        $this->state = $state;
        $this->jobName = "ProcessMaroRecordsFor{$companyName}-{$espAccountId}-{$this->state['recordType']}";
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->api = new MaroApi($this->espAccountId);


        try {
            JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
            $filterName = $this->getStep();
            $this->$filterName();
        } catch (JobCompletedException $e) {
            Log::notice($e->getMessage());
            exit;
        } catch (JobAlreadyQueuedException $e) {
            Log::notice($e->getMessage());
            exit;
        } catch (JobException $e) {
            $this->logJobException($e);

            if (in_array($e->getCode(), [JobException::NOTICE, JobException::WARNING, JobException::ERROR])) {
                $this->releaseJob($e);
            } else {
                throw $e;
            }
        } catch (\Exception $e) {
            $this->logUncaughtException($e);

            throw $e;
        }
    }

    protected function releaseJob(JobException $e)
    {
        $this->changeJobEntry(JobEntry::WAITING);
        $this->release($e->getDelay());
    }

    public function getStep()
    {
        if (is_null($this->state)) {
            $this->state['step'] = 0;
        }
        return $this->steps[$this->state['step']];
    }

    public function splitTypes()
    {
        $this->state['step']++;

        $types = ['opens', 'clicks', 'complaints', 'unsubscribes', 'bounces'];
        foreach ($types as $index => $currentType) {
            $this->state['recordType'] = $currentType;
            $this->queueNextJob();
        }
        //Launch Deliverable Job
        $this->state['step']++;
        $this->state['recordType'] = null;
        $this->queueNextJob();

        $this->changeJobEntry(JobEntry::SUCCESS);
    }

    protected function queueNextJob($queue = null, $delay = null)
    {
        $job = new ProcessThirdPartyMaroRecords(
            $this->companyName,
            $this->espAccountId,
            str_random(16),
            $this->state
        );

        if (!is_null($delay)) {
            $job->delay($delay);
        }

        if (!is_null($queue)) {
            $job->onQueue($queue);
        }

        $this->dispatch($job);
    }

    protected function savePagininatedRecords()
    {
        $this->state['pageNumber'] = isset($this->state['pageNumber']) ? $this->state['pageNumber'] : 1;
        if ($data = $this->pageHasData()) {
            $this->savePage($data);
            $this->state['pageNumber']++;
            $this->queueNextJob();
        }
        $this->changeJobEntry(JobEntry::SUCCESS, 0);
    }

    protected function changeJobEntry($status, $totalRows = 0)
    {
        JobTracking::changeJobState($status, $this->tracking, $totalRows);
    }

    protected function logJobException(JobException $e)
    {
        $logMethod = isset($this->logTypeMap[$e->getCode()]) ? $this->logTypeMap[$e->getCode()] : "error";

        Log::$logMethod(str_repeat('=', 20));
        Log::$logMethod('');
        Log::$logMethod(str_repeat('=', 20));
        Log::$logMethod($e->getMessage());

        if ($e->getCode() > JobException::NOTICE) {
            Log::$logMethod($e->getFile());
            Log::$logMethod($e->getLine());
            Log::$logMethod($e->getTraceAsString());
        }
    }

    protected function logUncaughtException($e)
    {
        Log::critical(str_repeat('=', 20));
        Log::critical('');
        Log::critical(str_repeat('=', 20));
        Log::critical($e->getMessage());
        Log::critical(str_repeat('#', 20) . 'Uncaught Exception' . str_repeat('#', 20));
    }

    private function pageHasData()
    {
        $this->api->constructDeliverableUrl($this->state['recordType'], $this->state['pageNumber']);

        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult($data);
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }

    private function processGuzzleResult($data)
    {
        if ($data->getStatusCode() != 200) {
            throw new JobException('API call failed.', JobException::NOTICE);
        }

        $data = $data->getBody()->getContents();
        return json_decode($data, true);
    }

    private function savePage($data)
    {
        $insert = array();
        foreach ($data as $currentRecord) {
            $insert[] = "( "
                . join(" , ", [
                    $currentRecord['account_id'],
                    $currentRecord['campaign_id'],
                    $currentRecord['contact_id'],
                    "'" .$this->state['recordType']."'" ,
                    (empty($currentRecord['ip_address']) ? "''" : "'" . $currentRecord['ip_address'] . "'"),
                    (empty($currentRecord['browser']) ? "''" : "'" . $currentRecord['browser'] . "'"),
                    (empty($currentRecord['recorded_at']) ? "''" : "'" . $currentRecord['recorded_at'] . "'"),
                    "'" .addslashes($currentRecord['contact']['email'])."'",
                    $this->espAccountId ,
                    $this->api->getAccountId() ,
                    'NOW()',
                    'NOW()'
                ])
                . ")";
        }
        if (count($insert) > 0) {
            DB::connection(snake_case($this->companyName.'Data'))->statement("
                    INSERT INTO maro_raw_actions
                        ( account_id , campaign_id , contact_id, action_type, ip_address,
                        browser , recorded_at , email_address , esp_account_id, account_number, created_at , 
                        updated_at )    
                    VALUES
                        " . join(' , ', $insert) . "
                    ON DUPLICATE KEY UPDATE
                        account_id = account_id ,
                        campaign_id = campaign_id ,
                        contact_id = contact_id,
                        action_type = action_type,
                        ip_address = ip_address,
                        browser = browser ,
                        recorded_at = recorded_at ,
                        created_at = created_at ,
                        email_address = email_address ,
                        esp_account_id = esp_account_id ,
                        account_number = account_number ,
                        updated_at = NOW()"
            );
        }
    }

    //will refactor if anything ever grows
    private function saveDeliveredPage($data)
    {
        $insert = array();
        foreach ($data as $currentRecord) {
            $insert[] = "( "
                . join(" , ", [
                    $currentRecord['account_id'],
                    $currentRecord['campaign_id'],
                    $currentRecord['contact_id'],
                    "'delivered'" ,
                    (empty($currentRecord['ip_address']) ? "''" : "'" . $currentRecord['ip_address'] . "'"),
                    (empty($currentRecord['browser']) ? "''" : "'" . $currentRecord['browser'] . "'"),
                    (empty($currentRecord['created_at']) ? "''" : "'" . $currentRecord['created_at'] . "'"),
                    "'" .addslashes($currentRecord['email'])."'",
                    $this->espAccountId ,
                    $this->api->getAccountId() ,
                    'NOW()',
                    'NOW()'
                ])
                . ")";
        }
        if (count($insert) > 0) {
            DB::connection(snake_case($this->companyName.'Data'))->statement("
                    INSERT INTO maro_raw_actions
                        ( account_id , campaign_id , contact_id, action_type, ip_address,
                        browser , recorded_at , email_address , esp_account_id, account_number, created_at , 
                        updated_at )    
                    VALUES
                        " . join(' , ', $insert) . "
                    ON DUPLICATE KEY UPDATE
                        account_id = account_id ,
                        campaign_id = campaign_id ,
                        contact_id = contact_id,
                        action_type = action_type,
                        ip_address = ip_address,
                        browser = browser ,
                        recorded_at = recorded_at ,
                        created_at = created_at ,
                        email_address = email_address ,
                        esp_account_id = esp_account_id ,
                        account_number = account_number ,
                        updated_at = NOW()"
            );
        }
    }

    public function pickCampaignsForSplit(){
        //Not going to page unless we find out we have to first page should be fine.
        $this->api->constructCampaignListUrl();
        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult($data);
        foreach($data as $campaign){
            if($campaign['status'] == "sent" && $campaign['send_at'] >= Carbon::today()->subDay(5)){
                $this->state['step']++;
                $this->state['campaign'] = $campaign['id'];
                $this->queueNextJob();
            }

        }
        $this->changeJobEntry(JobEntry::SUCCESS);
    }

    protected function savePaginatedDeliveredRecords()
    {
        $this->state['pageNumber'] = isset($this->state['pageNumber']) ? $this->state['pageNumber'] : 1;
        if ($data = $this->pageHasCampaignData()) {
            $this->saveDeliveredPage($data);
            $this->state['pageNumber']++;
            $this->queueNextJob();
        }
        $this->changeJobEntry(JobEntry::SUCCESS, 0);
    }

    private function pageHasCampaignData()
    {
        $campaignId = $this->state['campaign'];
        $this->api->setDeliverableLookBack();
        $this->api->setActionUrl($campaignId, "delivered", $this->state['pageNumber']);
        $data = $this->api->sendApiRequest();
        $data = $this->processGuzzleResult($data);

        if (empty($data)) {
            return false;
        } else {
            return $data;
        }

    }


}
