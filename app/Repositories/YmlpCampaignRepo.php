<?php

namespace App\Repositories;

use App\Models\YmlpCampaign;

class YmlpCampaignRepo {
    /**
     * @var IReport
     */
    protected $model;

    public function __construct(YmlpCampaign $model){
        $this->model = $model;
    }

    public function getMtCampaignNameForAccountAndDate($espAccountId, $date) {
        
        $whereClause = array(
            'esp_account_id' => $espAccountId, 
            'date' => $date
        );
        
        return $this->model->select('sub_id')->where($whereClause)->get()[0]['sub_id'];
    }

}