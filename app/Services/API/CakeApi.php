<?php

namespace App\Services\API;

use App\Facades\Guzzle;

class CakeApi {

  // A temporary stub because we will likely be internalizing this
  const ENDPOINT = "http://caridan.ampxl.net/app/websvc/cake/mt2/index.php?";
  const API_KEY = 'F9437Yjf*udfk39';
  private $priorDate;
  private $endDate;

  public function __construct($startDate, $endDate) {
    $this->priorDate = '&dtStart=' . $startDate;
    $this->endDate = '&endDate=' . $endDate;
  }

  public function sendApiRequest() {
    $url = $this->constructApiUrl();
    echo "URL: $url" . PHP_EOL;
    return Guzzle::get($url);
  }

  private function constructApiUrl() {
    return self::ENDPOINT . 'apiKey=' . self::API_KEY . $this->priorDate 
    . $this->endDate;
  }
}