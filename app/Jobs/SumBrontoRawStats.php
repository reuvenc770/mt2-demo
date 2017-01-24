<?php

namespace App\Jobs;

use App\Exceptions\JobException;
use App\Jobs\Job;
use App\Models\BrontoReport;
use App\Models\StandardReport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use DB;
class SumBrontoRawStats extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $tracking;
    const JOB_NAME = "SumBrontoRawStats";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tracking)
    {
        $this->tracking = $tracking;
        JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //This job is so specific that I felt i didnt want to add a bunch extra code to handle the sql
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
            try{
              $reports = BrontoReport::where("type","triggered")
                  ->select('message_name',DB::raw('sum(num_sends) as e_sent ,sum(num_deliveries) as delivered,
                        sum(num_bounces) as bounced,
                        sum(num_opens) as e_opens,
                        sum(uniq_opens) as e_opens_unique,
                        sum(num_clicks) as e_clicks,
                        sum(uniq_clicks) as e_clicks_unique'))
                  ->groupBy("message_name")->get();
                foreach($reports as $report){
                    $name = $report->message_name;
                    unset($report->message_name);//easier for the update
                    StandardReport::where("campaign_name",$name)->update($report->toArray());
                }
            } catch(\Exception $e){
                throw new JobException("Could not sum up Bronto Row Stats {$e->getMessage()}");
            }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }
    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

}
