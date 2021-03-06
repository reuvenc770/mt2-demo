<?php

namespace App\Reports;
use App\Repositories\EspApiAccountRepo;
use App\Repositories\EspRepo;
use App\Repositories\SuppressionRepo;
use Log;
use App\Facades\SlackLevel;
class SuppressionExportReport {
    private $suppressionRepo;
    private $espRepo;
    private $espAccountRepo;
    const SLACK_CHANNEL = "#mt2-daily-reports";
    const EMAIL_UNSUB = "espken@zetainteractive.com";


    public function __construct(SuppressionRepo $repo, EspRepo $espRepo, EspApiAccountRepo $accountRepo) {
        $this->suppressionRepo = $repo;
        $this->espRepo = $espRepo;
        $this->espAccountRepo = $accountRepo;
    }

    public function run($lookback) {
        $unsubCountArray = array();
        $esps = $this->espRepo->getAllEsps();
        foreach($esps as $esp){
         $espAccounts = $this->espAccountRepo->getAccountsbyEspWithSuppression($esp->id);
            $unsubCountArray[$esp->name]["totalHardbounces"] = 0;
            $unsubCountArray[$esp->name]["totalUnsubs"] = 0;
            $unsubCount = 0;
            if($espAccounts->count() == 0){
                unset($unsubCountArray[$esp->name]);
                continue;
            }
            foreach($espAccounts as $espAccount) {
                $hardBounces = $this->getRecordsByDateEsp($espAccount->id, $lookback, $this->suppressionRepo->getHardBounceId());
                $hardBounceCount = count($hardBounces);
                $unsubCountArray[$esp->name][$espAccount->account_name]["hardbounces"] = $hardBounceCount;
                $unsubCountArray[$esp->name]["totalHardbounces"] += $hardBounceCount;

                $unsubs = $this->getRecordsByDateEsp($espAccount->id, $lookback, $this->suppressionRepo->getUnsubId());
                $unsubCount = count($unsubs);
                $unsubCountArray[$esp->name][$espAccount->account_name]["unsubs"] = $unsubCount;
                $unsubCountArray[$esp->name]["totalUnsubs"] += $unsubCount;

            }
        }
        $this->notify($unsubCountArray,$lookback);
    }

    protected function getRecordsByDateEsp($espAccountId, $date, $typeId){
        try{
            return $this->suppressionRepo->getRecordsByDateIntervalEspType($typeId, $espAccountId, $date, "=");
        }
        catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying get Suppression Records for $typeId");
            throw new \Exception($e);
        }
    }

    public function notify($report,$date) {
        $output ="";
        foreach($report as $espName => $esp) {
            $output .= "*##### {$espName} Hard Bounce - Unsub Report for {$date} ####*\n";
            foreach ($esp as $espAccountName => $espAccount){
                if($espAccountName == "totalHardbounces"|| $espAccountName == "totalUnsubs"){
                    continue;
                }
                $output.= "*{$espAccountName}*  _Unsubs:_ {$espAccount["unsubs"]}  _HardBounces: {$espAccount["hardbounces"]}_\n";
            }
            $output .= "*##### {$espName}:  Hardbounces {$esp["totalHardbounces"]} -  Unsubscribes: {$esp["totalUnsubs"]}  ####*\n";
            $output.="\n\n";
        }
         SlackLevel::to(self::SLACK_CHANNEL)->send($output);

    }

    
}
