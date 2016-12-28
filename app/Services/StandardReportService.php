<?php

namespace App\Services;


class StandardReportService {
    protected $repo;

    public function __construct($reportRepo){
       $this->repo = $reportRepo;
    }

    public function insertStandardStats($data){
        $this->repo->insertStats($data);
    }

    public function getDeployId ( $internalEspId ) {
        return $this->repo->getDeployId( $internalEspId );
    }

    public function getInternalEspId ( $deployId ) {
        return $this->repo->getInternalEspId( $deployId );
    }

    public function getOrphanReportsByEsp(){
        return $this->repo->getOrphanReports();
    }

    public function convertStandardReport($reportId, $deploy){
        //kinda should be in the repo, but then again i dont want to pass in a full deploy into a repo
        $currentReport = $this->repo->getRow($reportId);
        $currentReport->external_deploy_id = $deploy->id;
        $currentReport->campaign_name = $deploy->deploy_name;
        $currentReport->name = $deploy->deploy_name;
        $currentReport->m_deploy_id = $deploy->id;

       return $currentReport->save();
    }
}
