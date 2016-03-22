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
      (subid_1, subid_2, email_id, subid_4, subid_5, clickDate, campaignDate, clicks, conversions, revenue, 
        created_at, updated_at) 
      VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
      ON DUPLICATE KEY UPDATE
        subid_1 = subid_1,
        subid_2 = subid_2,
        email_id = email_id,
        subid_4 = subid_4,
        subid_5 = subid_5,
        clickDate = clickDate,
        campaignDate = campaignDate,
        clicks = VALUES(clicks),
        conversions = VALUES(conversions),
        revenue = VALUES(revenue),
        updated_at = NOW(),
        created_at = created_at", 
        [
          $data['subid_1'],
          $data['subid_2'],
          $data['email_id'],
          $data['subid_4'], 
          $data['subid_5'], 
          $data['clickDate'],
          $data['campaignDate'],
          $data['clicks'], 
          $data['conversions'], 
          $data['revenue']
        ]

    );
  }

  public function getRecentInsertedStats($date) {
    return $this->report->where('campaignDate', '>=', $date)->groupBy('subid_1')->groupBy('subid_4')->get();
  }

  public function pullDeliverables($date) {
    return $this->report
      ->select(DB::raw('subid_1 AS campaign_id,
        email_id,
        SUM(clicks) AS clicks,
        SUM(conversions) AS conversions,
        SUM(revenue) AS revenue,
        MIN(clickDate) AS first_click,
        MAX(clickDate) AS last_click'))
      ->where('campaignDate', '>=', $date)
      ->groupBy('subid_1')
      ->groupBy('email_id')
      ->get();
  }

}