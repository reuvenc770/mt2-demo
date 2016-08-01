<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\CompanyInfo;

class CompanyInfoRepo {
    protected $model;

    public function __construct ( CompanyInfo $model ) {
        $this->model = $model;
    }

    public function getDeploysForAdvertiser($advertiser) {
        return $this->model
             ->join('advertiser_info as ai', 'company_info.company_id', '=', 'ai.company_id')
             ->join('EspAdvertiserJoin as eaj', 'ai.advertiser_id', '=', 'eaj.advertiserID')
             ->where('company_info.company_name', $advertiser)
             ->select('eaj.subAffiliateID')
             ->get();
    }

    public function pullForSync() {
        return $this->model->get();
    }
}