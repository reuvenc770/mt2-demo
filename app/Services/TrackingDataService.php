<?php
/**
 * Created by: rbertorelli
*/

namespace App\Services;
use App\Repositories\TrackingRepo;
use App\Services\Interfaces\IDataService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;

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
    $reportStats = $this->api->sendApiRequest();
    
    $out = $this->processGuzzleResult($reportStats);
    return $out;
  }

  public function insertApiRawStats($data) {

    $convertedRows = array();

    foreach ($data as $row) {
      $convertedRow = $this->mapToRawReport($row);
      $convertedRows[]= $convertedRow;
      $this->repo->insertStats($convertedRow);
    }
    Event::fire(new RawReportDataWasInserted($this, $convertedRows));
  }

  public function insertSegmentedApiRawStats($data, $length) {
    $start = 0;
    $end = 5000;

    while ($end < $length) {
      $slice = array_slice($data, $start, $end);
      $this->insertApiRawStats($slice);
      $start = $end;
      $end = $end + 5000;
    } 
  }

  protected function processGuzzleResult($data) {
      $data = $data->getBody()->getContents();
      return json_decode($data, true);
  }

  protected function mapToRawReport($row) {
    return $row;
  }

  public function mapToStandardReport($data) {
    return [
      'subid_1' => $data['subid_1'],
      't_clicks' => $data['clicks'],
      'conversions' => $data['conversions'],
      'revenue' => $data['revenue'],
    ];
  }

  public function insertCsvRawStats($data) {}
}