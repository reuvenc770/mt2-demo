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

  public function insertAggregateStats($data) {
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

  public function insertRecordStats ( $data ) {
      DB::connection( 'reporting_data' )->statement( "
        INSERT INTO
            cake_conversions ( email_id , s1 , s2 , s4 , s5 , click_date , campaign_date , click_id , conversion_date , conversion_id , request_session_id , affiliate_id , offer_id , advertiser_id , campaign_id , creative_id , user_agent_string , price_received , price_paid , price_paid_currency_id , price_received_currency_id , ip , created_at , updated_at )    
        VALUES
            ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , NOW() , NOW() )
        ON DUPLICATE KEY UPDATE
            email_id = email_id ,
            s1 = s1 ,
            s2 = s2 , 
            s4 = s4 ,
            s5 = s5 ,
            click_date = click_date ,
            campaign_date = campaign_date ,
            click_id = click_id ,
            conversion_date = VALUES( conversion_date ) ,
            conversion_id = VALUES( conversion_id ) ,
            request_session_id = request_session_id ,
            affiliate_id = affiliate_id ,
            offer_id = offer_id ,
            advertiser_id = advertiser_id ,
            campaign_id = campaign_id ,
            creative_id = creative_id ,
            user_agent_string = VALUES( user_agent_string ) ,
            price_received = VALUES( price_received ) ,
            price_paid = VALUES( price_paid ) ,
            price_paid_currency_id = VALUES( price_paid_currency_id ) ,
            price_received_currency_id = VALUES( price_received_currency_id ) ,
            ip = VALUES( ip ) ,
            created_at = created_at ,
            updated_at = NOW()
        " , [
            $data[ 'email_id' ] ,
            $data[ 's1' ] ,
            $data[ 's2' ] ,
            $data[ 's4' ] ,
            $data[ 's5' ] ,
            $data[ 'click_date' ] ,
            $data[ 'campaign_date' ] ,
            is_null( $data[ 'click_id' ] ) ? 0 : $data[ 'click_id' ] ,
            $data[ 'conversion_date' ] ,
            is_null( $data[ 'conversion_id' ] ) ? 0 : $data[ 'conversion_id' ] ,
            is_null( $data[ 'request_session_id' ] ) ? 0 : $data[ 'request_session_id' ] ,
            is_null( $data[ 'affiliate_id' ] ) ? 0 : $data[ 'affiliate_id' ] ,
            is_null( $data[ 'offer_id' ] ) ? 0 : $data[ 'offer_id' ] ,
            is_null( $data[ 'advertiser_id' ] ) ? 0 : $data[ 'advertiser_id' ] ,
            is_null( $data[ 'campaign_id' ] ) ? 0 : $data[ 'campaign_id' ] ,
            is_null( $data[ 'creative_id' ] ) ? 0 : $data[ 'creative_id' ] ,
            $data[ 'user_agent_string' ] ,
            $data[ 'price_received' ] ,
            $data[ 'price_paid' ] ,
            $data[ 'price_paid_currency_id' ] ,
            $data[ 'price_received_currency_id' ] ,
            $data[ 'ip' ]
        ] );

  }

  public function getRecentInsertedStats($date) {
    return $this->report->where('campaignDate', '>=', $date)->groupBy('subid_1')->groupBy('subid_4')->get();
  }

  public function pullDeliverables($date) {
    return $this->report
      ->join(config('database.connections.mysql.database') . ".user_agent_strings", 'cake_aggregated_data.user_agent_string', '=', 'user_agent_strings.user_agent_string')
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
