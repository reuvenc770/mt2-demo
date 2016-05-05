<?php

namespace App\Repositories;
use App\Models\Interfaces\IReport;
use DB;
//TODO We should make some magic methods like get*FromDeployID (use scopes) since i can see us pulling a lot from that relationship
class StandardApiReportRepo {
    /**
     * @var IReport
     */
    protected $report;

    public function __construct(IReport $report){
        $this->report = $report;
    }

    public function insertStats($data) {
        $this->report->updateOrCreate(array('campaign_name' => $data['campaign_name']), $data);
    }

    public function getCampaigns($espAccountId, $date) {
        return $this->report
            ->select('external_deploy_id', 'campaign_name', 'esp_account_id', 'esp_internal_id', 'datetime')
            ->where( 'created_at' , ">=" , $date )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();
    }

    public function getEspToInternalMap($espAccountId) {
        // need an appropriate limit
        // According to Danny, residuals after a month don't matter
        $result = $this->report
            ->select('esp_internal_id', 'external_deploy_id')
            ->where( 'created_at' , ">=" , DB::raw('CURDATE() - INTERVAL 31 DAY') )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();

        $output = array();

        foreach ($result as $row) {
            $output[$row['esp_internal_id']] = $row['external_deploy_id'];
        }

        return $output;
    }

    public function getDateFromDeployId($deployId){
        return $this->report->select('datetime')
            ->where('m_deploy_id', $deployId)
            ->first();
    }

    public function getInternalIdFromDeployId($deployId){
        return $this->report->select('esp_internal_id')
                ->where('m_deploy_id', $deployId)
                ->first();
    }

}