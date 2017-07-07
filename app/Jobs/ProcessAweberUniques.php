<?php

namespace App\Jobs;

use App\Exceptions\JobException;
use App\Jobs\Job;
use App\Models\AWeberReport;
use App\Models\JobEntry;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use App\Facades\JobTracking;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use Cache;
use App\Services\EspApiAccountService;

class ProcessAweberUniques extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $jobName = 'ProcessAweberUniques-';
    CONST KEY_NAME = "AWEBER_RATE_LIMIT_";
    protected $tracking;
    protected $id;
    protected $espAccountId;
    protected $type;
    protected $infoUrl;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $espAccoiuntId, $url, $type, $tracking)
    {

        $this->tracking = $tracking;
        $this->infoUrl = $url;
        $this->id = $id;
        $this->espAccountId = $espAccoiuntId;
        $this->type = $type;
        $name = "{$this->jobName}-{$type}-{$id}";
        JobTracking::startAggregationJob($name, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( EspApiAccountService $espServ )
    {
        if ( $espServ->statsEnabledForAccount( $this->espAccountId ) ) {
            if(Cache::get(self::KEY_NAME.$this->espAccountId) == 1){
                $this->release(0);
            };

            $time = Carbon::now()->addMinutes(120);
            Cache::put(self::KEY_NAME.$this->espAccountId,1,$time);//lock this account

            $reportService = APIFactory::createAPIReportService("AWeber", $this->espAccountId);
            try {
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                switch ($this->type) {
                    case AWeberReport::UNIQUE_OPENS:
                        $value = $reportService->getUniqueStatForCampaignUrl($this->infoUrl, AWeberReport::UNIQUE_OPENS);
                        Cache::forget(self::KEY_NAME.$this->espAccountId);//give up the key
                        $reportService->updateUniqueStatForCampaignUrl($this->id, $this->type, $value);
                        break;
                    case AWeberReport::UNIQUE_CLICKS:
                        $value = $reportService->getUniqueStatForCampaignUrl($this->infoUrl, AWeberReport::UNIQUE_CLICKS);
                        Cache::forget(self::KEY_NAME.$this->espAccountId);//give up the key
                        $reportService->updateUniqueStatForCampaignUrl($this->id, $this->type, $value);
                        break;
                    default:
                        throw new JobException("Not a valid action type");
                }

                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            } catch (\Exception $e) {
                Cache::forget(self::KEY_NAME.$this->espAccountId);
                throw new JobException("{$this->jobName} failed with {$e->getMessage()}  {$e->getLine()}" . PHP_EOL);
            }
        } else {
            \Log::warning( 'ESP Account ' . $this->espAccountId . ' from Aweber is not enabled for loading stats. Uniques data import is being aborted.' );
        }
    }

    public function failed() {
        Cache::forget(self::KEY_NAME.$this->espAccountId);
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
