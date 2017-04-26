<?php
/**
 * Created by: rbertorelli
*/

namespace App\Services;
use App\Repositories\TrackingRepo;
use App\Services\Interfaces\IDataService;
use App\Services\Interfaces\IApi;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use Carbon\Carbon;
use App\Repositories\EspApiAccountRepo;

class TrackingDataService implements IDataService
{

  protected $repo;
  protected $api;
  private $espAccountRepo;

  public function __construct(TrackingRepo $repo, IApi $api, EspApiAccountRepo $espAccountRepo) {
    $this->repo = $repo;
    $this->api = $api;
    $this->espAccountRepo = $espAccountRepo;
  }

  public function retrieveApiStats($data = null) {
    $reportStats = $this->api->sendApiRequest( $data );
    
    $out = $this->processGuzzleResult($reportStats);

    return $out;
  }

  public function insertApiRawStats( $data , $conversions = false) {
      if ( $conversions ) {
          $this->insertApiRawRecordStats( $data , \App::make( \App\Repositories\Attribution\RecordReportRepo::class ) );
      } else {
          $this->insertTrackingActions( $data );
      }
  }

  protected function insertTrackingActions($data) {
    foreach ($data as $row) {
      $convertedRow = $this->mapToActions($row);
      $this->repo->insertAction($convertedRow);
    }

    // need to get data at a different level of aggregation for std report
    $convertedRows = $this->repo->getRecentInsertedStats();
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
          $this->insertTrackingActions( $slice );
      }

      $start = $end;
      $end = $end + 5000;
    } 
  }

  protected function processGuzzleResult($data) {
      $data = $data->getBody()->getContents();
      return json_decode($data, true);
  }

  protected function mapToActions($row) {
      return [
          'email_id' => $this->extractEidFromS2($row['s2']),
          'deploy_id' => (int)$row['s1'],
          'action_id' => $row['conversion_id'] ? 3 : 2,
          'datetime' => $row['datetime'],
          'esp_account_id' => $this->convertToEspAccountId($row['s4']),

          'subid_1' => $row['s1'],
          'subid_2' => $row['s2'],
          'subid_4' => $row['s4'],
          'subid_5' => $row['s5'],
          'click_id' => $row['click_id'],
          'conversion_id' => $row['conversion_id'],
          'cake_affiliate_id' => $row['affiliate_id'],
          'cake_advertiser_id' => $row['advertiser_id'],
          'cake_offer_id' => $row['offer_id'],
          'cake_creative_id' => $row['creative_id'],
          'cake_campaign_id' => $row['campaign_id'],
          'ip_address' => $row['ip_address'],
          'request_session_id' => $row['request_session_id'],
          'user_agent_string' => urldecode($row['user_agent']),
          'revenue' => $row['price_received'],
          'carrier' => $row['carrier'],
      ];
  }

  public function mapToStandardReport($data) {
    return [
      'external_deploy_id' => $data['deploy_id'],
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
  
  public function setRetrieveApiLimit ($limit) {}

    private function convertToEspAccountId($name) {
        $result = $this->espAccountRepo->getIdFromName($name);

        return $result ? $result->id : 0;
    }
}
