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
            ->orderBy('datetime', 'desc')
            ->get();
    }

    public function getActionsCampaigns($espAccountId, $date) {
        // Much like the above, that it pulls recent campaigns,
        // but also pulls those that are part of workflows 
        // (and thus might be "old" but are still run regularly)
        $db = config('database.connections.mysql.database');

        return $this->report
                    ->select('external_deploy_id', 'campaign_name', 'esp_account_id', 'esp_internal_id', 'datetime')
                    ->leftJoin("$db.esp_workflow_steps as ews", 'standard_reports.external_deploy_id', '=', 'ews.deploy_id')
                    ->whereRaw("esp_account_id = $espAccountId AND ( ( standard_reports.created_at >= '$date' AND datetime >= '$date' - INTERVAL 31 DAY ) OR ews.deploy_id IS NOT NULL)")
                    ->orderBy('datetime', 'desc')
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

    public function getStatsForDeploy($deployId) {
        return $this->report
                    ->select('delivered as delivers', 'e_opens as opens', 'e_clicks as clicks')
                    ->where('external_deploy_id', $deployId)
                    ->first();
    }

}
