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

    public function __construct(SuppressionRepo $repo )
    {
        $this->repo = $repo;
    }
    
    public function recordRawHardBounce($espId,$email,$esp_internal_id,$reason, $date){
        return $this->recordSuppression($espId,$email,$esp_internal_id,$date, Suppression::TYPE_HARD_BOUNCE);
    }

    public function recordRawComplaint($espId,$email,$esp_internal_id,$reason, $date){
        return $this->recordSuppression($espId,$email,$esp_internal_id,$date, Suppression::TYPE_COMPLAINT);
    }

    public function recordRawUnsub($espId,$email,$esp_internal_id,$reason, $date){
        return $this->recordSuppression($espId,$email,$esp_internal_id,$date, Suppression::TYPE_UNSUB);
    }

    private function recordSuppression($espId,$email,$esp_internal_id,$date, $type){
        $record = $this->buildRecord($espId,$email,$esp_internal_id,$date, $type);
        try{
            $this->repo->insertSuppression($record);
        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying to record unsub");
            throw new \Exception($e);
        }
        return true;
    }

    private function buildRecord($espId,$email,$esp_internal_id,$date,$type){
        return  array(
            "esp_account_id" => $espId,
            "email_address"  => $email,
            "esp_internal_id"    => $esp_internal_id,
            "date"       => $date,
            "reason_id"        => $this->getReasonCode($espId, $type)
        );
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

    public function getReasonCode($esp_account_id, $type_id){
        $reason = $this->repo->getReasonByAccountType($esp_account_id,$type_id);
        return $reason->id;
    }
}