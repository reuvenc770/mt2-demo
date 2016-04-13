<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 3:59 PM
 */

namespace App\Services;


use App\Repositories\SuppressionRepo;
use Log;
//TODO could refactor, but not sure where suppression is going
class SuppressionService
{
    protected $repo;

    public function __construct(SuppressionRepo $repo)
    {
        $this->repo = $repo;
    }



    //reason will have an lookup table once i know the options.
    public function recordRawHardBounce($espId,$email,$campaignId,$reason, $date){
        $rawRecord = array(
            "esp_account_id" => $espId,
            "email_address"  => $email,
            "campaign_id"    => $campaignId,
            "date"       => $date,
            "reason"        => $reason //Will be INT once we see whats returned
        );
        try{
            $this->repo->insertHardBounce($rawRecord);
        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying to record hard bounce");
            throw new \Exception($e);
        }
    }

    public function recordRawUnsub($espId,$email,$campaignId,$reason, $date){
        $rawRecord = array(
            "esp_account_id" => $espId,
            "email_address"  => $email,
            "campaign_id"    => $campaignId,
            "date"       => $date,
            "reason"        => $reason //Will be INT once we see whats returned
        );
        try{
            $this->repo->insertUnsub($rawRecord);
        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying to record unsub");
            throw new \Exception($e);
        }
    }
}