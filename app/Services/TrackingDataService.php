<?php
/**
 * Created by: rbertorelli
*/

namespace App\Services;
use App\Repositories\TrackingRepo;
use App\Services\Interfaces\ITrackingService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawTrackingDataWasInserted;

class TrackingDataService implements ITrackingService
{

  protected $repo;
  protected $api;
  protected $source;

  public function __construct($source, TrackingRepo $repo, $api) {
    $this->repo = $repo;
    $this->api = $api;
    $this->source = $source;
  }

  public function retrieveTrackingApiStats() {
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

    Event::fire(new RawTrackingDataWasInserted($this->source, $convertedRows));
  }

  protected function processGuzzleResult($data) {
      $data = $data->getBody()->getContents();
      return json_decode($data, true);
  }

  protected function mapToRawReport($row) {
    return $row;
  }
}