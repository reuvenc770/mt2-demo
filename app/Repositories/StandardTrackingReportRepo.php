<?php

namespace App\Repositories;
use App\Models\Interfaces\IReport;
use DB;

class StandardTrackingReportRepo {
    /**
     * @var IReport
     */
    protected $report;

    public function __construct(IReport $report){
        $this->report = $report;
    }

    public function insertStats($data) {
        $updateArray = array(
            "t_clicks" => $data['t_clicks'],
            "conversions" => $data['conversions'],
            "revenue" =>$data['revenue'],
        );
      $this->report->where('external_deploy_id', $data['external_deploy_id'])->update($updateArray);
    }

}