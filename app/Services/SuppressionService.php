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
use App\Repositories\EmailCampaignStatisticRepo;

class SuppressionService
{
    protected $repo;
    protected $statRepo;

    public function __construct(SuppressionRepo $repo, EmailCampaignStatisticRepo $statRepo )
    {
        $this->repo = $repo;
        $this->statRepo = $statRepo;
    }

    public function recordRawHardBounce($espId,$email,$espInternalId, $date){
        return $this->recordSuppression($espId,$email,$espInternalId, $date, Suppression::TYPE_HARD_BOUNCE);
    }

    public function recordRawComplaint($espId,$email,$espInternalId, $date){
        return $this->recordSuppression($espId,$email,$espInternalId,$date, Suppression::TYPE_COMPLAINT);
    }

    public function recordRawUnsub($espId, $email, $espInternalId, $date){
        return $this->recordSuppression($espId, $email ,$espInternalId, $date, Suppression::TYPE_UNSUB);
    }

    public function recordSuppression($espId, $email, $espInternalId, $date, $type){
        $record = $this->buildRecord($espId, $email, $espInternalId, $date, $type);
        try{
            $this->repo->insertSuppression($record);

            if (Suppression::TYPE_HARD_BOUNCE === $type) {
                $this->statRepo->updateHardBounce($email, $espInternalId);
            }
            else {
                $this->statRepo->updateUnsubStatus($email, $espInternalId);
            }
            
        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying to record unsub");
            throw new \Exception($e);
        }
        return true;
    }

    private function buildRecord($espId, $email, $espInternalId, $date, $type){
        $record =  array(
            "esp_account_id" => $espId,
            "email_address"  => $email,
            "esp_internal_id"    => $espInternalId,
            "date"       => $date,
            "type_id" => $type,
            "reason_id"        => $this->getReasonCode($espId, $type)
        );
        if($record["esp_internal_id"] == 0){
            unset($record["esp_internal_id"]);
        }
        return $record;
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

    public function recordSuppressionByReason($email, $date, $reason){
        $record = array(
            "esp_account_id" => 0,
            "email_address"  => $email,
            "esp_internal_id"    => 0,
            "date"       => $date,
            "type_id" => $this->getTypeByReason($reason),
            "reason_id"        => $reason
        );
        try{
            $this->repo->insertSuppression($record);
        } catch (\Exception $e) {
            Log::error($e->getMessage(). ": while trying to record unsub");
            throw new \Exception($e);
        }
        return true;
    }


    public function convertSuppressionReason($response){
        $mt2Reasons = array();
        foreach($response->suppression as $suppression){
            if(!isset($suppression->suppressionReasonDetails)){
                continue;
            }
            if($suppression->suppressionReasonDetails !== "Suppression via MT2 Import"){
                $suppression->suppressionReasonDetails = $this->repo->convertReasonFromLegacy($suppression->suppressionReasonDetails);
            } else {
                // its a MT2 import lets clear the list
                $response->suppression = array();
            }
            $reasons = $this->repo->getAllSuppressionsForEmail($suppression->email_addr);
            foreach ($reasons as $reason){
                $mt2Reasons[] = array(
                    "email_addr" => $suppression->email_addr,
                    "suppressionReasonDetails" => $reason->suppressionReason->display_status,
                    "espAccountName" => isset($reason->espAccount->account_name) ? $reason->espAccount->account_name : '',
                    "campaignName" => isset($reason->standardReport->campaign_name) ? $reason->standardReport->campaign_name: ''
                );
            }


        }
        $response->suppression = array_merge($response->suppression, $mt2Reasons);
        return json_encode($response);
    }
    public function getAllSuppressionsSinceDate($date){
        return $this->repo->getAllSinceDate($date);
    }
    public function getTypeByReason($reason){
        $reason = $this->repo->getReasonById($reason);
        return $reason->suppression_type;
    }
    public function getReasonCode($esp_account_id, $type_id){
        $reason = $this->repo->getReasonByAccountType($esp_account_id,$type_id);
        return $reason->id;
    }

    public function listAllReasons(){
        return $this->repo->getReasonList();
    }

    public function espSuppressionsForDateRange($espId, $lookback) {
        return $this->repo->espSuppressionsForDateRange($espId, $lookback);
    }

    public function getAllSuppressionsDateRange ( array $dateRange ) {
        return $this->repo->getAllSuppressionsDateRange( $dateRange );
    }

    public function getByInternalEmailDate ( $internalEspId , $emailAddress , $date ) {
        return $this->repo->getByInternalEmailDate( $internalEspId , $emailAddress , $date );
    }
}
