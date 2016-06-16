<?php

namespace App\Reports;
use League\Csv\Writer;
use Cache;
use Maknz\Slack\Facades\Slack;
use Illuminate\Database\Query\Builder;
use App\Models\Suppression;
use App\Repositories\SuppressionRepo;

class BlueHornetSuppressionExportReport {
    private $repo;
    private $range = false;
    private $hardbounces;
    private $unsubs;
    const SLACK_CHANNEL = '#gtddev'; #"#mt2-daily-reports";
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

            $this->hardbounces = $this->getRecordsByDateEsp($espAccount->id, $lookback, $this->range);
            $this->exportBounces($id, $name);
            $this->unsubs = $this->getRecordsByDateEsp($espAccount->id, $lookback, $this->range);
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
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($this->hardbounces->toArray());

        if ($this->range) {
            $this->destination->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookback}_TO_TODAY_{$name}_HB.csv", $writer->__toString());
            $this->destination->append("ALL_UNSUB_HARDBOUNCE/{$this->lookback}_TO_TODAY_ALL_HB.csv", $writer->__toString());
        }
        else {
            Cache::tags($this->espName)->increment("{$id}_hb_count",count($this->hardbounces));
            Cache::tags($this->espName)->increment("{$this->espName}_hb_total",count($this->hardbounces));

            $this->destination->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookback}_{$name}_HB.csv", $writer->__toString());
            $this->destination->append("ALL_UNSUB_HARDBOUNCE/{$this->lookback}_ALL_HB.csv", $writer->__toString());
        }
    }

    protected function exportUnsubs($id, $name) {
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($this->unsubs->toArray());

        if ($this->range) {
            $this->destination->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookback}_TO_TODAY_{$name}_unsubs.csv", $writer->__toString());
            $this->destination->append("ALL_UNSUB_HARDBOUNCE/{$this->lookback}_TO_TODAY_ALL_UNSUB.csv", $writer->__toString());
        }
        else {
            Cache::tags($this->espName)->increment("{$id}_unsub_count",count($this->unsubs));
            Cache::tags($this->espName)->increment("{$this->espName}_unsub_total",count($this->unsubs));

            $this->destination->put("DAILY_UNSUB_HARDBOUNCE/{$this->lookback}_{$name}_unsubs.csv", $writer->__toString());
            $this->destination->append("ALL_UNSUB_HARDBOUNCE/{$this->lookback}_ALL_UNSUB.csv", $writer->__toString());
        }
    }

    protected function getRecordsByDateEsp($espAccountId, $date, $typeId, $useRange = false){
        try{
            $operator = $useRange ? '>=' : '=';
            return $this->repo->getRecordsByDateIntervalEspType($typeId, $espAccountId, $date, $operator);
        } 
        catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying get Suppression Records for $typdId");
            throw new \Exception($e);
        }
    }

    public function setRange() {
        $this->range = true;
    }
    
}
