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
    DB::statement("
      INSERT INTO cake_aggregated_data 
      (advertiser_id, affiliate_id, offer_id, creative_id, subid_1, subid_2, 
        subid_3, subid_4, subid_5, date, clicks, conversions, revenue, 
        created_at, updated_at) 
      VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
      ON DUPLICATE KEY UPDATE
        advertiser_id = advertiser_id,
        affiliate_id = affiliate_id,
        offer_id = offer_id,
        creative_id = creative_id,
        subid_1 = subid_1,
        subid_1 = subid_2,
        subid_3 = subid_3,
        subid_4 = subid_4,
        subid_5 = subid_5,
        date = date,
        clicks = VALUES(clicks),
        conversions = VALUES(conversions),
        revenue = VALUES(revenue),
        updated_at = NOW(),
        created_at = created_at", 
        [
          "{$data['advertiser_id']}", "{$data['affiliate_id']}", "{$data['offer_id']}", 
          "{$data['creative_id']}", "{$data['subid_1']}", "{$data['subid_2']}", 
          "{$data['subid_3']}", "{$data['subid_4']}", "{$data['subid_5']}", "{$data['date']}", 
          "{$data['clicks']}", "{$data['conversions']}", "{$data['revenue']}"
        ]

    );
  }

}