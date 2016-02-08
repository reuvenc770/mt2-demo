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
        
        // Custom query needed - 
        // unfortunately, CAKE only has one item useful for matching:
        // the s1 value, which is the first part of the deploy_id
        // so we need to use LIKE

        DB::statement("
            UPDATE 
                standard_reports
            SET
                t_clicks = ?,
                conversions = ?,
                revenue = ?
            WHERE
                deploy_id LIKE CONCAT(?, '%')",
        [
            $data['t_clicks'],
            $data['conversions'],
            $data['revenue'],
            $data['subid_1'] . '_'
        ]);
    }

}