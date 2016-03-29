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
        $this->report->updateOrCreate(array('deploy_id' => $data['deploy_id']), $data);
    }

}