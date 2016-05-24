<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 4:00 PM
 */

namespace App\Repositories;

use App\Models\Suppression;

class SuppressionRepo
{
    protected $suppressionModel;

    public function __construct(Suppression $suppression)
    {
        $this->suppressionModel = $suppression;
    }

    public function insertHardBounce($arrayData)
    {
        $arrayData["type_id"] = Suppression::TYPE_HARD_BOUNCE;
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "esp_account_id" => $arrayData["esp_account_id"],
            "campaign_id" => $arrayData['campaign_id']], $arrayData);
    }

    public function insertUnsub($arrayData){
        $arrayData["type_id"] = Suppression::TYPE_UNSUB;
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "esp_account_id" => $arrayData["esp_account_id"],
            "campaign_id" => $arrayData['campaign_id']], $arrayData);
    }

    public function insertComplaint($arrayData){
        $arrayData["type_id"] = Suppression::TYPE_COMPLAINT;
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "esp_account_id" => $arrayData["esp_account_id"],
            "campaign_id" => $arrayData['campaign_id']], $arrayData);
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

}