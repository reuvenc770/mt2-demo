<?php

namespace App\Repositories;
use App\Models\Interfaces\IReport;
use DB;

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
            ->select('external_deploy_id', 'campaign_name', 'esp_account_id', 'esp_internal_id', 'datetime')
            ->where( 'updated_at' , ">=" , $date )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();
    }

    public function getEspToInternalMap($espAccountId) {
        // need an appropriate limit
        // According to Danny, residuals after a month don't matter
        $result = $this->report
            ->select('esp_internal_id', 'external_deploy_id')
            ->where( 'updated_at' , ">=" , DB::raw('CURDATE() - INTERVAL 31 DAY') )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();

        $output = array();

        foreach ($result as $row) {
            $output[$row['esp_internal_id']] = $row['external_deploy_id'];
        }

        return $output;
    }

}