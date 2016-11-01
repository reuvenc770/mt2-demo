<?php
/**
 * Created by: rbertorelli
*/

namespace App\Services;
use App\Repositories\TrackingRepo;
use App\Repositories\EmailCampaignStatisticRepo;
use App\Services\Interfaces\IDataService;
use App\Repositories\Interfaces\CakeTargetRepo;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use Carbon\Carbon;

class TrackingDataService implements IDataService
{

  protected $repo;
  protected $api;
  protected $source;

  public function __construct($source, TrackingRepo $repo, $api) {
    $this->repo = $repo;
    $this->api = $api;
    $this->source = $source;
  }

  public function retrieveApiStats($data = null) {
    $reportStats = $this->api->sendApiRequest( $data );
    
    $out = $this->processGuzzleResult($reportStats);

    return $out;
  }

  public function insertApiRawStats( $data , $recordLevel = false) {
      if ( $recordLevel ) {
            $this->insertApiRawRecordStats( $data , \App::make( \App\Repositories\Attribution\RecordReportRepo::class ) );
      } else {
            $this->insertApiRawAggregateStats( $data );
      }
  }

  protected function insertApiRawAggregateStats ( $data ) {
    foreach ($data as $row) {
      $convertedRow = $this->mapToRawReport($row);
      $this->repo->insertAggregateStats($convertedRow);
    }

    // need to get data at a different level of aggregation for std report
    $convertedRows = $this->repo->getRecentInsertedStats($this->api->startDate);
    Event::fire(new RawReportDataWasInserted($this, $convertedRows));
  }

  protected function insertApiRawRecordStats ( $data , $reportRepo ) {
    foreach ( $data as $row ) {
        $row[ 'email_id' ] = $this->extractEidFromS2( $row[ 's2' ] );

        $this->repo->insertRecordStats( $row );

        if ( $row[ 'price_received' ] > 0 ) {
            $date = $row[ 'conversion_date' ];

            if ( $date === '0000-00-00 00:00:00' ) {
                $date = $row[ 'click_date' ];
            }

            $reportRepo->insertAction( [
                "email_id" => $row[ 'email_id' ] ,
                "deploy_id" => $row[ 's1' ] ,
                "offer_id" => 0 ,
                "delivered" => 0 ,
                "opened" => 0 ,
                "clicked" => 0 ,
                "converted" => 1 , 
                "bounced" => 0 ,
                "unsubbed" => 0 ,
                "revenue" => $row[ 'price_received' ] ,
                "date" => Carbon::parse( $date )->toDateString()
            ] );
        }
    }
  }

  public function insertSegmentedApiRawStats($data, $length , $recordLevel = false ) {
    $start = 0;
    $end = 5000;
    $recordRepo = null;

    while ( $end < $length ) {
      $slice = array_slice($data, $start, $end);

      if ( $recordLevel ) {
          if ( is_null( $recordRepo ) ) {
            $recordRepo = \App::make( \App\Repositories\Attribution\RecordReportRepo::class );
          }

          $this->insertApiRawRecordStats( $slice , $recordRepo );
      } else {
          $this->insertApiRawAggregateStats( $slice );
      }

      $start = $end;
      $end = $end + 5000;
    } 
  }

  protected function processGuzzleResult($data) {
      $data = $data->getBody()->getContents();
      return json_decode($data, true);
  }

  protected function mapToRawReport($row) {
    return [
      'subid_1' => $row['subid_1'],
      'subid_2' => $row['subid_2'],
      'subid_4' => $row['subid_4'],
      'subid_5' => $row['subid_5'],
      'email_id' => $this->extractEidFromS2($row['subid_2']),
      'user_agent_string' => urldecode($row['user_agent_string']),
      'affiliate_id' => $row['affiliateID'],
      'clicks' => $row['clicks'],
      'conversions' => $row['conversions'],
      'revenue' => $row['revenue'],
      'clickDate' => $row['clickDate'],
      'campaignDate' => $row['campaignDate'],
    ];
  }

  public function mapToStandardReport($data) {
    return [
      'external_deploy_id' => $data['subid_1'],
      't_clicks' => $data['clicks'],
      'conversions' => $data['conversions'],
      'revenue' => $data['revenue'],
    ];
  }

  public function insertCsvRawStats($data, $date) {}


  private function extractEidFromS2($s2) {
    // s2 is of the form 2741865382_0_0_0_0_0_0_0_0_2016-03-17
    // we need 2741865382

    $arr = explode('_', $s2);

    if (sizeof($arr) > 0) {
      $eid = $arr[0];

      if (is_numeric($eid)) {
        return (int)$eid;
      }
    }

    return 0;
  }

}
