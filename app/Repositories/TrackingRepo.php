<?php

namespace App\Repositories;

use DB;

class TrackingRepo
{
  /**
   * @var IReport
  */
  protected $report;

  public function __construct($report) {
    $this->report = $report;
  }

  public function insertStats($data) {
    // The UPSERT here shaves off several minutes in runtime in tests
    // The select-insert approach takes close to 3x longer to run

    DB::connection("reporting_data")->statement("
      INSERT INTO cake_aggregated_data 
      (subid_1, subid_4, subid_5, clicks, conversions, revenue, 
        created_at, updated_at) 
      VALUES 
        (?, ?, ?, ?, ?, ?, NOW(), NOW())
      ON DUPLICATE KEY UPDATE
        subid_1 = subid_1,
        subid_4 = subid_4,
        subid_5 = subid_5,
        clicks = VALUES(clicks),
        conversions = VALUES(conversions),
        revenue = VALUES(revenue),
        updated_at = NOW(),
        created_at = created_at", 
        [
          $data['subid_1'],  
          $data['subid_4'], 
          $data['subid_5'], 
          $data['clicks'], 
          $data['conversions'], 
          $data['revenue']
        ]

    );
  }

}