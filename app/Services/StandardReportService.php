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

}
