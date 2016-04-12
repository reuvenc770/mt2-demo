<?php

namespace App\Repositories;
use App\Models\Interfaces\IReport;

class StandardApiReportRepo {
    /**
     * @var IReport
     */
    protected $report;

    public function __construct(IReport $report){
        $this->report = $report;
    }

    public function insertStats($data) {
        $this->report->updateOrCreate(array('external_deploy_id' => $data['external_deploy_id']), $data);
    }

    public function getCampaigns($espAccountId, $date) {
        return $this->report
            ->where( 'updated_at' , ">=" , $date )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();
    }

}