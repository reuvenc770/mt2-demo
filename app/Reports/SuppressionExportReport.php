<?php

namespace App\Reports;
use Cache;
use Maknz\Slack\Facades\Slack;
use App\Repositories\SuppressionRepo;
use Log;
class SuppressionExportReport {
    private $repo;
    private $range = false;
    private $hardbounces;
    private $unsubs;
    const SLACK_CHANNEL = "#mt2-daily-reports";
    private $destination;

    public function __construct(SuppressionRepo $repo, $espName, $espAccounts, $destination) {
        $this->repo = $repo;
        $this->espName = $espName;
        $this->espAccounts = $espAccounts;
        $this->destination = $destination;
    }

    public function execute($lookback) {
        $this->lookback = $lookback;

        foreach ($this->espAccounts as $espAccount) {
            $name = $espAccount->account_name;
            $id = $espAccount->id;

            $this->hardbounces = $this->getRecordsByDateEsp($espAccount->id, $lookback, $this->repo->getHardBounceId());
            $this->exportBounces($id, $name);
            $this->unsubs = $this->getRecordsByDateEsp($espAccount->id, $lookback, $this->repo->getUnsubId());
            $this->exportUnsubs($id, $name);
        }
    }

    public function notify() {

        $output = "*##### {$this->espName} Hard Bounce - Unsub Report for {$this->lookback} ####*\n\n";

         foreach ($this->espAccounts as $espAccount){
             $localUnsub = Cache::tags(array($this->espName))->get("{$espAccount->id}_unsub_count");
             $localHB = Cache::tags(array($this->espName))->get("{$espAccount->id}_hb_count");
             $output.= "*{$espAccount->account_name}*  _Unsubs:_ {$localUnsub}  _HardBounces: {$localHB}_\n";
         }

         $unsubTotal = Cache::tags($this->espName)->get("{$this->espName}_unsub_total");
         $output.= "\n\n*##Total Unsubs for {$this->espName}:* : {$unsubTotal}\n";
         $hardBounceTotal = Cache::tags(array($this->espName))->get("{$this->espName}_hb_total");
         $output.= "*##Total HardBounces for {$this->espName}:* : {$hardBounceTotal}\n";
         Slack::to(self::SLACK_CHANNEL)->send($output);
         Cache::tags($this->espName)->flush();
    }

    protected function exportBounces($id, $name) {
            Cache::tags($this->espName)->increment("{$id}_hb_count",count($this->hardbounces));
            Cache::tags($this->espName)->increment("{$this->espName}_hb_total",count($this->hardbounces));

    }

    protected function exportUnsubs($id, $name) {

            Cache::tags($this->espName)->increment("{$id}_unsub_count",count($this->unsubs));
            Cache::tags($this->espName)->increment("{$this->espName}_unsub_total",count($this->unsubs));
    }

    protected function getRecordsByDateEsp($espAccountId, $date, $typeId){
        try{
            $operator = $this->range ? '>=' : '=';
            return $this->repo->getRecordsByDateIntervalEspType($typeId, $espAccountId, $date, $operator);
        } 
        catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying get Suppression Records for $typeId");
            throw new \Exception($e);
        }
    }

    public function setRange() {
        $this->range = true;
    }
    
}
