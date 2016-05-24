<?php

namespace App\Services;

use App\Repositories\StandardApiReportRepo;


class StandardReportService {
    protected $repo;

    public function __construct(StandardApiReportRepo $reportRepo){
       $this->repo = $reportRepo;
    }

    public function insertStandardStats($data){
        $this->repo->insertStats($data);
    }

}