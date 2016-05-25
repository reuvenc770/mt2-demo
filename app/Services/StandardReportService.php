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

}