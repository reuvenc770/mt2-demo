<?php

namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use League\Csv\Writer;
use Cache;
use Maknz\Slack\Facades\Slack;
use App;
use Storage;
//TODO eventually we should have a query to csv/report tool that sits outside of this onetime job, if the need comes up
class ParseAndSendSuppressions extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "BH Daily Usub Report";
    protected $espAccounts;
    protected $espAccountName;
    protected $espAccountId;
    protected $espName;
    protected $lookBack;
    CONST SLACK_CHANNEL = "#mt2-daily-reports";
    protected $tracking;
    protected $range;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($espAccounts, $espName, $espAccountName, $espAccountId, $lookBack, $tracking, $range = false)
    {
        $this->espAccounts = $espAccounts;
        $this->espAccountName = $espAccountName;
        $this->espName = $espName;
        $this->espAccountId = $espAccountId;
        $this->lookBack = $lookBack;
        $this->tracking = $tracking;
        $this->range = $range;
        JobTracking::startEspJob(self::JOB_NAME,$this->espName, $this->espAccountId, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $subscriptionService = APP::make("App\\Services\\SuppressionService");
        //TODO Dirty as hell but limited time job
        if($this->range){
            $hardbounces = $subscriptionService->getHardBouncesByDateEsp($this->espAccountId, $this->lookBack, true);
            $unsubs = $subscriptionService->getUnsubsByDateEsp($this->espAccountId, $this->lookBack, true);

            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $writer->insertAll($hardbounces->toArray());
            Storage::disk("hornet7")->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookBack}_TO_TODAY_{$this->espAccountName}_HB.csv", $writer->__toString());

            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $writer->insertAll($unsubs->toArray());
            Storage::disk("hornet7")->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookBack}_TO_TODAY_{$this->espAccountName}_unsubs.csv", $writer->__toString());

            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $writer->insertAll($hardbounces->toArray());
            Storage::disk("hornet7")->append("ALL_UNSUB_HARDBOUNCE/{$this->lookBack}_TO_TODAY_ALL_HB.csv", $writer->__toString());

            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $writer->insertAll($unsubs->toArray());
            Storage::disk("hornet7")->append("ALL_UNSUB_HARDBOUNCE/{$this->lookBack}_TO_TODAY_ALL_UNSUB.csv", $writer->__toString());
            $this->delete();
        }

        $hardbounces = $subscriptionService->getHardBouncesByDateEsp($this->espAccountId, $this->lookBack);
        Cache::tags($this->espName)->increment("{$this->espAccountId}_hb_count",count($hardbounces));
        Cache::tags($this->espName)->increment("{$this->espName}_hb_total",count($hardbounces));


        $unsubs = $subscriptionService->getUnsubsByDateEsp($this->espAccountId, $this->lookBack);
        Cache::tags($this->espName)->increment("{$this->espAccountId}_unsub_count",count($unsubs));
        Cache::tags($this->espName)->increment("{$this->espName}_unsub_total",count($unsubs));


        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($hardbounces->toArray());
        Storage::disk("hornet7")->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookBack}_{$this->espAccountName}_HB.csv", $writer->__toString());

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($unsubs->toArray());
        Storage::disk("hornet7")->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookBack}_{$this->espAccountName}_unsubs.csv", $writer->__toString());

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($hardbounces->toArray());
        Storage::disk("hornet7")->append("ALL_UNSUB_HARDBOUNCE/{$this->lookBack}_ALL_HB.csv", $writer->__toString());

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($unsubs->toArray());
        Storage::disk("hornet7")->append("ALL_UNSUB_HARDBOUNCE/{$this->lookBack}_ALL_UNSUB.csv", $writer->__toString());

        Cache::tags($this->espName)->decrement("{$this->espName}_accounts_to_go");

     if(Cache::tags($this->espName)->get("{$this->espName}_accounts_to_go") == 0){
        $output = "*##### {$this->espName} Hard Bounce - Unsub Report for {$this->lookBack} ####*\n\n";

         foreach ($this->espAccounts as $espAccount){
             $localUnsub = Cache::tags(array($this->espName))->get("{$espAccount->id}_unsub_count");
             $localHB = Cache::tags(array($this->espName))->get("{$espAccount->id}_hb_count");
             $output.= "*{$espAccount->account_name}*  _Unsubs:_ {$localUnsub}  _HardBounces: {$localHB}_\n";
         }

         $unsubs = Cache::tags($this->espName)->get("{$this->espName}_unsub_total");
         $output.= "\n\n*##Total Unsubs for {$this->espName}:* : {$unsubs}\n";
         $hardBounces = Cache::tags(array($this->espName))->get("{$this->espName}_hb_total");
         $output.= "*##Total HardBounces for {$this->espName}:* : {$hardBounces}\n";
         Slack::to(self::SLACK_CHANNEL)->send($output);
         Cache::tags($this->espName)->flush();
     }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }


    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
