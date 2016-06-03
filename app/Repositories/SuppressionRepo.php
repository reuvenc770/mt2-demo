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

    public function insertSuppression($arrayData){
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "reason_id" => $arrayData['reason_id']], $arrayData);
    }

    public function getRecordsByDateEspType($typeId, $espAccountId, $date){
       return $this->suppressionModel->select("email_address","reason")
                                ->where("type_id",$typeId)
                                ->where("esp_account_id",$espAccountId)
                                ->where("date",$date )
                                ->get();
    }

    public function getRecordsByDateToCurrentEspType($typeId, $espAccountId, $date){
        return $this->suppressionModel->select("email_address","reason")
            ->where("type_id",$typeId)
            ->where("esp_account_id",$espAccountId)
            ->where("date",'>=', $date )
            ->get();
    }

    public function getReasonByAccountType($espAccountId, $typeId){
        return $this->suppressionReason->select('suppression_reasons.id')->where('suppression_type',$typeId)
                                ->join('esp_accounts', 'esp_accounts.esp_id', '=','suppression_reasons.esp_id')
                                ->where('esp_accounts.id',$espAccountId)->first();

    }
    public function getAllSuppressionsForEmail($email){
        return $this->suppressionModel->where('email_address', $email)->get();
    }
    public function getReasonList(){
        return $this->suppressionReason->select('id as value' , 'display_status as name')->displayable()->get();
    }

    public function convertReasonFromLegacy($reason){
        return $this->suppressionReason->select('display_status as name')->where('legacy_status',$reason)->first()->name;
    }

    public function getReasonById($reason){
        return $this->suppressionReason->find($reason);
    }

}