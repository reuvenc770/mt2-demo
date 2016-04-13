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

}