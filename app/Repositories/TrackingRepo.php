<?php

namespace App\Repositories;
use DB;
use Carbon\Carbon;

class TrackingRepo
{
    /**
    * @var IReport
    */
    protected $report;

    public function __construct($report) {
        $this->report = $report;
    }

    public function insertAction($data) {
        DB::connection("reporting_data")->statement("
          INSERT INTO cake_actions
          (email_id, deploy_id, action_id, datetime, esp_account_id, subid_1, subid_2,
           subid_4, subid_5, click_id, conversion_id, cake_affiliate_id, cake_advertiser_id, 
           cake_offer_id, cake_creative_id, cake_campaign_id, ip_address, request_session_id,
            user_agent, carrier, revenue, created_at, updated_at)
          VALUES

            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())

          ON DUPLICATE KEY UPDATE
            email_id = email_id,
            deploy_id = deploy_id,
            action_id = action_id,
            datetime = datetime,
            esp_account_id = esp_account_id,
            subid_1 = subid_1,
            subid_2 = subid_2,
            email_id = email_id,
            subid_4 = subid_4,
            subid_5 = subid_5,
            click_id = click_id,
            conversion_id = conversion_id,
            cake_affiliate_id = cake_affiliate_id,
            cake_advertiser_id = cake_advertiser_id,
            cake_offer_id = cake_offer_id,
            cake_creative_id = cake_creative_id,
            cake_campaign_id = cake_campaign_id,
            ip_address = ip_address,
            request_session_id = request_session_id,
            user_agent = user_agent,
            carrier = carrier,
            revenue = VALUES(revenue),
            created_at = created_at,
            updated_at = NOW()", 
            [
              $data['email_id'],
              $data['deploy_id'],
              $data['action_id'],
              $data['datetime'], 
              $data['esp_account_id'], 
              $data['subid_1'],
              $data['subid_2'],
              $data['subid_4'],
              $data['subid_5'],
              $data['click_id'], 
              $data['conversion_id'], 
              $data['cake_affiliate_id'],
              $data['cake_advertiser_id'],
              $data['cake_offer_id'],
              $data['cake_creative_id'],
              $data['cake_campaign_id'],
              $data['ip_address'],
              $data['request_session_id'],
              $data['user_agent_string'],
              $data['carrier'],
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

    public function getRecentInsertedStats() {
        $activeSearchDate = Carbon::today()->subDays(5)->toDateTimeString();

        return DB::connection('reporting_data')->select("SELECT
            deploy_id,
            SUM(IF(action_id = 2, 1, 0)) as clicks,
            SUM(IF(action_id = 3, 1, 0)) as conversions,
            SUM(ca.revenue) as revenue
        FROM
            cake_actions ca
            INNER JOIN standard_reports sr ON ca.deploy_id = sr.external_deploy_id
        WHERE
            sr.datetime >= ?
        GROUP BY
            deploy_id", [$activeSearchDate]);
    }

    public function pullDeliverables($date) {
        $db = config('database.connections.mysql.database');
        return $this->report
            ->join("$db.user_agent_strings as uas", 'cake_actions.user_agent', '=', 'uas.user_agent_string')
            ->join("standard_reports as sr", 'cake_actions.deploy_id', '=', 'sr.external_deploy_id')
            ->selectRaw('deploy_id AS campaign_id, 
                email_id, 
                SUM(IF(action_id = 2, 1, 0)) AS clicks, 
                SUM(IF(action_id = 3, 1, 0)) AS conversions, 
                SUM(cake_actions.revenue) AS revenue, 
                MIN(CASE WHEN action_id = 2 THEN cake_actions.datetime ELSE NULL END) AS first_click, 
                MAX(CASE WHEN action_id = 2 THEN cake_actions.datetime ELSE NULL END) AS last_click, 
                uas.id AS uas_id')
            ->where('sr.datetime', '>=', $date)
            ->groupBy('email_id')
            ->groupBy('deploy_id')
            ->groupBy('uas.id')
            ->get();
    }

    public function pullUserAgents($lookback) {
        return $this->report->select('user_agent_string')->where('clickDate', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"))->get();
    }

    public function getEmailSortedCakeActions(Carbon $startPoint, $startEmailId, $limit) {
        $dataSchema = config('database.connections.mysql.database');
        $table = $this->report->getTable();
        
        return $this->report
            ->select( "email_id" , "deploy_id", 
                DB::raw('DATE(datetime) as date'), 
                "d.esp_account_id",
                "d.offer_id",
                'co.vertical_id',
                DB::raw('IFNULL(e.email_domain_id, 0) as email_domain_id'),
                DB::raw('COUNT(IF(action_id = 2, 1, 0)) as clicks'), 
                DB::raw('SUM(IF(action_id = 3, 1, 0)) as conversions'),
                DB::raw('MAX(datetime) as last_datetime'))
            ->whereBetween("datetime", [
                $startPoint->toDateTimeString(), 
                Carbon::today()->endOfDay()->toDateTimeString()
            ])
            ->join("$dataSchema.deploys as d", $table . '.deploy_id', '=', 'd.id')
            ->join("$dataSchema.cake_offers as co", $table.'.cake_offer_id', '=', 'co.id')
            ->leftJoin("$dataSchema.emails as e", $table.'.email_id', '=', 'e.id')
            ->groupBy('email_id', 'deploy_id', 'date', 'co.vertical_id')
            ->take($limit)
            ->orderBy('last_datetime', 'asc')
            ->get();
    }

    public function getPaidConversionsByCakeOfferDeploy ( $dateRange , $cakeOfferId , $deployId ) {
        return $this->report
                    ->where( [ 
                        [ 'action_id' , '=' , '3' ] , #conversion
                        [ 'revenue' , '>' , 0 ] , #has revenue
                        [ 'cake_offer_id' , '=' , $cakeOfferId ] ,
                        [ 'deploy_id' , '=' , $deployId ] ,
                        [ 'datetime' , '>=' , $dateRange[ 'start' ] ] ,
                        [ 'datetime' , '<' , $dateRange[ 'end' ] ] ,
                    ] ); 
    }

    public function getPaidConversionsByCakeOfferFeedDeploy ( $dateRange , $cakeOfferId , $feedId , $deployId ) {
        $table = $this->report->getTable();

        return $this->report
                    ->join( 'deploy_snapshots as ds' , function ( $join ) use ( $table ) {
                        $join->on( $table . '.email_id' , '=' , 'ds.email_id' );
                        $join->on( $table . '.deploy_id' , '=' , 'ds.deploy_id' );
                    } )->where( [ 
                        [ 'ds.feed_id' , '=' , $feedId ] ,
                        [ $table . '.action_id' , '=' , '3' ] , #conversion
                        [ $table . '.revenue' , '>' , 0 ] , #has revenue
                        [ $table . '.cake_offer_id' , '=' , $cakeOfferId ] ,
                        [ $table . '.deploy_id' , '=' , $deployId ] ,
                        [ $table . '.datetime' , '>=' , $dateRange[ 'start' ] ] ,
                        [ $table . '.datetime' , '<' , $dateRange[ 'end' ] ] ,
                    ] ); 
    }

    public function getPaidDeployAndCakeOffers ( $dateRange ) {
        $dataSchema = config('database.connections.mysql.database');
        $table = $this->report->getTable();

        return $this->report
                    ->join( $dataSchema . '.mt_offer_cake_offer_mappings' , $table . '.cake_offer_id' , '=' , $dataSchema . '.mt_offer_cake_offer_mappings.cake_offer_id' )
                    ->where( [ 
                        [ $table . '.action_id' , '=' , '3' ] , #conversion
                        [ $table . '.revenue' , '>' , 0 ] , #has revenue
                        [ $table . '.datetime' , '>=' , $dateRange[ 'start' ] ] ,
                        [ $table . '.datetime' , '<' , $dateRange[ 'end' ] ] ,
                    ] )
                    ->select( $table . '.deploy_id' , $table . '.cake_offer_id' , $dataSchema . '.mt_offer_cake_offer_mappings.offer_id' )
                    ->groupBy( $table . '.deploy_id' , $table . '.cake_offer_id' , $dataSchema . '.mt_offer_cake_offer_mappings.offer_id' )
                    ->orderBy( $table . '.cake_offer_id' )
                    ->get();
    }
}
