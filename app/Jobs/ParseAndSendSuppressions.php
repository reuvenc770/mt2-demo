<?php

namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use League\Csv\Writer;
use Cache;
use Maknz\Slack\Facades\Slack;
use App;
use Storage;
//TODO eventually we should have a query to csv/report tool that sits outside of this onetime job, if the need comes up
class ParseAndSendSuppressions extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $espAccounts;
    protected $espAccountName;
    protected $espAccountId;
    protected $espName;
    protected $lookBack;
    CONST SLACK_CHANNEL = "#mt2-daily-reports";
    protected $tracking;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($espAccounts, $espName, $espAccountName, $espAccountId, $lookBack, $tracking)
    {
        $this->espAccounts = $espAccounts;
        $this->espAccountName = $espAccountName;
        $this->espName = $espName;
        $this->espAccountId = $espAccountId;
        $this->lookBack = $lookBack;
        $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subscriptionService = APP::make("App\\Services\\SuppressionService");
        $hardbounces = $subscriptionService->getHardBouncesByDateEsp($this->espAccountId, $this->lookBack);
        Cache::tags($this->espName)->increment("{$this->espAccountId}_hb_count",count($hardbounces));
        Cache::tags($this->espName)->increment("{$this->espName}_hb_total",count($hardbounces));


        $unsubs = $subscriptionService->getUnsubsByDateEsp($this->espAccountId, $this->lookBack);
        Cache::tags($this->espName)->increment("{$this->espAccountId}_unsub_count",count($unsubs));
        Cache::tags($this->espName)->increment("{$this->espName}_unsub_total",count($unsubs));

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($hardbounces->toArray());
        $writer->insertAll($unsubs->toArray());
        Storage::disk("hornet7")->put("hardbounce_unsub/{$this->lookBack}_{$this->espAccountName}_UnsubHB.csv", $writer->__toString());

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
    }
}
