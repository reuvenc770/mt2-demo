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
    // last user agent might update

    DB::connection("reporting_data")->statement("
      INSERT INTO cake_aggregated_data 
      (subid_1, subid_2, email_id, subid_4, subid_5, affiliate_id, user_agent_string, clickDate, campaignDate, clicks, conversions, revenue, 
        created_at, updated_at) 
      VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
      ON DUPLICATE KEY UPDATE
        subid_1 = subid_1,
        subid_2 = subid_2,
        email_id = email_id,
        subid_4 = subid_4,
        subid_5 = subid_5,
        affiliate_id = affiliate_id,
        user_agent_string = VALUES(user_agent_string),
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
          $data['affiliate_id'],
          $data['user_agent_string'],
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
    $dataDB = env('DB_DATABASE', '');
    return $this->report
      ->join($dataDb . ".user_agent_strings", 'cake_aggregated_data.user_agent_string', '=', 'user_agent_strings.user_agent_string')
      ->select(DB::raw('subid_1 AS campaign_id,
        email_id,
        SUM(clicks) AS clicks,
        SUM(conversions) AS conversions,
        SUM(revenue) AS revenue,
        MIN(clickDate) AS first_click,
        MAX(clickDate) AS last_click,
        user_agent_strings.id AS uas_id'))
      ->where('campaignDate', '>=', $date)
      ->groupBy('subid_1')
      ->groupBy('email_id')
      ->get();
  }

  public function pullUserAgents($lookback) {
    return $this->report->select('user_agent_string')->where('clickDate', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"))->get();
  }

}