<?php

namespace App\Services;

use App\Repositories\StandardReportRepo;

class StandardReportService {
    protected $repo;

    public function __construct(StandardReportRepo $reportRepo){
       $this->repo = $reportRepo;
    }

    public function insertStandardStats($data){
        //$this->repo->insertStats($standardReport);

        // Custom query needed - 
        // unfortunately, CAKE only has one item useful for matching:
        // the s1 value, which is the first part of the deploy_id
        // so we need to use LIKE

        DB::statement("
            UPDATE 
                standard_reports
            SET
                t_creative_id = ?,
                t_offer_id = ?,
                t_clicks = ?,
                conversions = ?,
                revenue = ?
            WHERE
                deploy_id LIKE ?%
        ",
        [
            $data['t_creative_id'],
            $data['t_offer_id'],
            $data['t_clicks'],
            $data['conversions'],
            $data['revenue'],
            $data['subid_1'] . '_'
        ]);
    }

}