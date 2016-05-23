<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 3:59 PM
 */

namespace App\Services;


use App\Models\Suppression;
use App\Repositories\SuppressionRepo;
use Log;
//TODO could refactor, but not sure where suppression is going in terms of direction so leaving it simple
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

    public function recordRawComplaint($espId,$email,$campaignId,$reason, $date){
        $rawRecord = array(
            "esp_account_id" => $espId,
            "email_address"  => $email,
            "campaign_id"    => $campaignId,
            "date"       => $date,
            "reason"        => $reason //Will be INT once we see whats returned
        );
        try{
            $this->repo->insertComplaint($rawRecord);
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

    public function getHardBouncesByDateEsp($espAccountId, $date, $useRange = false){
        try{

        $method = $useRange ? "getRecordsByDateToCurrentEspType" : "getRecordsByDateEspType";
        return $this->repo->$method(Suppression::TYPE_HARD_BOUNCE, $espAccountId, $date);

        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying get Suppression Records by Hardbounce");
            throw new \Exception($e);
        }
    }
    public function getUnsubsByDateEsp($espAccountId, $date, $useRange = false){
        try{
            $method = $useRange ? "getRecordsByDateToCurrentEspType" : "getRecordsByDateEspType";
           return $this->repo->$method(Suppression::TYPE_UNSUB, $espAccountId, $date);
        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying get Suppression Records by Unsub");
            throw new \Exception($e);
        }

    }
}