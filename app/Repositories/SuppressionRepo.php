<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 4:00 PM
 */

namespace App\Repositories;

use App\Models\Suppression;
use App\Models\SuppressionReason;

class SuppressionRepo
{
    protected $suppressionModel;
    protected $suppressionReason;

    public function __construct(Suppression $suppression, SuppressionReason $reason)
    {
        $this->suppressionModel = $suppression;
        $this->suppressionReason = $reason;
    }

    public function insertHardBounce($arrayData)
    {
        $arrayData["type_id"] = Suppression::TYPE_HARD_BOUNCE;
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "reason_id" => $arrayData['reason_id']], $arrayData);
    }

    public function insertUnsub($arrayData){
        $arrayData["type_id"] = Suppression::TYPE_UNSUB;
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "reason_id" => $arrayData['reason_id']], $arrayData);
    }

    public function insertComplaint($arrayData){
        $arrayData["type_id"] = Suppression::TYPE_COMPLAINT;
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "reason_id" => $arrayData['reason_id']], $arrayData);
    }

    public function getRecordsByDateEspType($type_id, $espAccountId, $date){
       return $this->suppressionModel->select("email_address","reason")
                                ->where("type_id",$type_id)
                                ->where("esp_account_id",$espAccountId)
                                ->where("date",$date )
                                ->get();
    }

    public function getRecordsByDateToCurrentEspType($type_id, $espAccountId, $date){
        return $this->suppressionModel->select("email_address","reason")
            ->where("type_id",$type_id)
            ->where("esp_account_id",$espAccountId)
            ->where("date",'>=', $date )
            ->get();
    }

    public function getReasonByAccountType($esp_account_id, $type_id){
        return $this->suppressionReason->where('suppression_type',$type_id)
                                ->join('esp_accounts', 'esp_accounts.esp_id', '=','suppression_reasons.esp_id')
                                ->where('esp_accounts.id',$esp_account_id)->first();

    }

}